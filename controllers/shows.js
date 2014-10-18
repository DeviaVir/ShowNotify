var secrets = require('../config/secrets');
var User = require('../models/User');
var Show = require('../models/Show');
var querystring = require('querystring');
var validator = require('validator');
var async = require('async');
var cheerio = require('cheerio');
var request = require('request');
var _ = require('underscore');
var graph = require('fbgraph');
var Github = require('github-api');
var Twit = require('twit');
var tvDB = require('thetvdb-api');

/**
 * GET /api
 * List of currently subscribed shows
 */

exports.getShows = function(req, res) {
  var currentShows = req.user.shows,
      key = secrets.thetvdb.apiKey,
      shows = [],
      c = 0,
      total = currentShows.length;

  currentShows.forEach(function(showId) {
    tvDB(key).getSeriesById(showId, function(error, result) {
      c++;
      if(('Data' in result) && result.Data !== 0 && ('Series' in result.Data)) {
        shows.push(result.Data.Series);

        if(c >= total) {
          res.render('shows/index', {
            title: 'Shows',
            shows: shows
          });
        }
      }
    });
  });
};

/**
 * GET /shows/id/:id
 * TheTVDB Show a certain TV show
 */

exports.getId = function(req, res) {
  return showId(req, res);
};

/**
 * POST /shows/id/:id
 * TheTVDB Show a certain TV show
 */

exports.postId = function(req, res) {
  var id = req.params.id,
      user = req.user;

  // Add show to followlist for this user
  if(user.shows.indexOf(id) > -1) {
    var i = user.shows.indexOf(id);
    user.shows.splice(i);
  }
  else {
    user.shows.push(id);
  }
  user.save();

  // Add to shows
  Show.findById(id, function(err, currentShow) {
    console.log(err, currentShow);

    if(!currentShow) {
      var show = new Show({
        id: id
      });

      show.save(function(err) {
        if (err) {
          if (err.code === 11000) {
            req.flash('errors', { msg: 'Show with that id already exists.' });
          }
        }
      });
    }
  });

  return showId(req, res);
};

/**
 * GET /shows/search
 * TheTVDB Search
 */

exports.getSearch = function(req, res) {
  res.render('shows/search', {
    title: 'Search for shows',
    noresult: false
  });
};

/**
 * GET /shows/search/:name
 * TheTVDB Search
 */

exports.postSearch = function(req, res) {
  req.assert('name', 'Name cannot be blank').notEmpty();
  var errors = req.validationErrors();

  if (errors) {
    req.flash('errors', errors);
    return res.redirect('/shows/search');
  }

  var name = req.body.name;
  var key = secrets.thetvdb.apiKey;
  tvDB(key).getSeries(name, function(error,result){
    if(('Data' in result) && result.Data !== 0 && ('Series' in result.Data)) {
      var serie = result.Data.Series;
      if(Object.prototype.toString.call(serie) == '[object Object]') {
        serie = [serie];
      }

      res.render('shows/results', {
        title: 'Shows for ' + name,
        name: name,
        series: serie,
        noresult: false
      });
    }
    else {
      res.render('shows/search', {
        title: 'Search for shows',
        noresult: true
      });
    }
  });
};

function showId(req, res) {
  var id = req.params.id,
      key = secrets.thetvdb.apiKey;
  tvDB(key).getSeriesById(id, function(error,result){
    if(('Data' in result) && result.Data !== 0 && ('Series' in result.Data)) {
      var serie = result.Data.Series,
          actors = serie.Actors.split('|'),
          genres = serie.Genre.split('|');

      var firstActor = actors.shift(),
          lastActor = actors.pop(),
          firstGenre = genres.shift(),
          lastGenre = genres.pop();

      var following = false;
      if(req.user.shows.indexOf(id) > -1) {
        following = true;
      }

      actors = actors.join(', ');
      genres = genres.join(', ');
      res.render('shows/id', {
        title: 'TV Show',
        serie: serie,
        actors: actors,
        genres: genres,
        following: following
      });
    }
    else {
      res.render('shows/search', {
        title: 'Search for shows',
        noresult: true
      });
    }
  });
}
