var secrets = require('../config/secrets'),
    tvDB = require('thetvdb-api');

/**
 * GET /
 * Home page.
 */

exports.index = function(req, res) {
  if(req.user) {
    var currentShows = req.user.shows,
        key = secrets.thetvdb.apiKey,
        shows = [],
        c = 0,
        total = currentShows.length;

    if(currentShows.length > 0) {
      currentShows.forEach(function(showId) {
        tvDB(key).getSeriesById(showId, function(error, result) {
          c++;
          if(('Data' in result) && result.Data !== 0 && ('Series' in result.Data)) {
            shows.push(result.Data.Series);
            if(c >= total) {
              res.render('home', {
                title: 'Home',
                shows: shows
              });
            }
          }
        });
      });
    }
    else {
      emptyHome(req, res);
    }
  }
  else {
    emptyHome(req, res);
  }
};

function emptyHome(req, res) {
  res.render('home', {
    title: 'Home',
    shows: []
  });
}
