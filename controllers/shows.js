var secrets = require('../config/secrets');
var User = require('../models/User');
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
  res.render('shows/index', {
    title: 'Shows',
    shows: []
  });
};

/**
 * GET /shows/id/:id
 * TheTVDB Show a certain TV show
 */

exports.getId = function(req, res) {
  var id = req.params.id;
  var key = secrets.thetvdb.apiKey;
  tvDB(key).getSeriesById(id, function(error,result){
    if(('Data' in result) && result.Data !== 0 && ('Series' in result.Data)) {
      var serie = result.Data.Series;
      console.log(serie);
      res.render('shows/id', {
        title: 'TV Show',
        serie: serie
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
      res.render('shows/results', {
        title: 'Shows for ' + name,
        name: name,
        series: result.Data.Series
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