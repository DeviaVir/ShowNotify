var app = require('../app');
var http = require('http');
var xml = require('xml2js').parseString;
var colors = require('colors');

var secrets = require('../config/secrets');
var Show = require('../models/Show');
var User = require('../models/User');

var nodemailer = require('nodemailer');
var transporter = nodemailer.createTransport("SMTP", {
  service: 'gmail',
  auth: {
    user: secrets.email.username,
    pass: secrets.email.password
  }
});

if (!Date.now) {
  Date.now = function now() {
    return new Date().getTime();
  };
}

function pad(a,b) { return(1e15+a+"").slice(-b); }

var tomorrowDate = new Date(new Date().getTime() + 24 * 60 * 60 * 1000),
    yesterdayDate = new Date(new Date().getTime() - 24 * 60 * 60 * 1000);

var tomorrow = tomorrowDate.getFullYear() + '-' + ( tomorrowDate.getMonth() + 1 ) + '-' + tomorrowDate.getDate(),
    yesterday = yesterdayDate.getFullYear() + '-' + ( yesterdayDate.getMonth() + 1 ) + '-' + yesterdayDate.getDate();

Show.find({}, function(err, shows) {
  if(err === null) {
    shows.forEach(function(show) {
      var tomorrows  = 'http://www.thetvdb.com/api/GetEpisodeByAirDate.php?apikey=' + secrets.thetvdb.apiKey + '&seriesid=' + show.id + '&airdate=' + tomorrow,
          yesterdays = 'http://www.thetvdb.com/api/GetEpisodeByAirDate.php?apikey=' + secrets.thetvdb.apiKey + '&seriesid=' + show.id + '&airdate=' + yesterday;

      // Get tomorrow
      http.get(tomorrows, function(res) {
        res.setEncoding('utf8');
        res.on('data', function (body) {
          xml(body, function(err, obj) {
            if(obj.Data && ('Error' in obj.Data) && obj.Data.Error) {
              console.info('Skipping tomorrow, no relevant airDate'.green, show.id);
            }
            else {
              show.users.forEach(function(userId) {
                User.findById(userId, function(user) {
                  var mailOptions = {
                    from: 'ShowNotify ✔ <' + secrets.email.username + '>',
                    to: user.email,
                    subject: show.name + ' returns tomorrow - ShowNotify',
                    text: 'Look sharp! ' + show.name + ' returns tomorrow with season ' + pad(obj.Data.Episode[0].SeasonNumber, 2) + ' episode ' + pad(obj.Data.Episode[0].EpImgFlag, 2) + ' (s' + pad(obj.Data.Episode[0].SeasonNumber, 2) + 'e' + pad(obj.Data.Episode[0].EpImgFlag, 2) + ') it has been called "' + obj.Data.Episode[0].EpisodeName[0] + '" and this is a short overview: ' + obj.Data.Episode[0].Overview[0],
                    html: 'Look sharp! <br /><br />' +
                      '<strong>' + show.name + '</strong> returns tomorrow with season ' + pad(obj.Data.Episode[0].SeasonNumber, 2) + ' episode ' + pad(obj.Data.Episode[0].EpImgFlag, 2) + ' (s' + pad(obj.Data.Episode[0].SeasonNumber, 2) + 'e' + pad(obj.Data.Episode[0].EpImgFlag, 2) + ').<br /><br />' +
                      '<strong>"' + obj.Data.Episode[0].EpisodeName[0] + '"</strong><br />' + obj.Data.Episode[0].Overview[0]
                  };
                  transporter.sendMail(mailOptions, function(error, info){
                    console.log(error, info);
                  });
                });
              });
            }
          });
        });
      }).on('error', function(e) {
        console.error('Got http error: ' + e.message + ''.underline.red);
      });

      // Get yesterday
      http.get(yesterdays, function(res) {
        res.setEncoding('utf8');
        res.on('data', function (body) {
          xml(body, function(err, obj) {
            if(obj.Data && ('Error' in obj.Data) && obj.Data.Error) {
              console.info('Skipping yesterday, no relevant airDate'.green, show.id);
            }
            else {
              show.users.forEach(function(userId) {
                User.findById(userId, function(err, user) {
                  var mailOptions = {
                    from: 'ShowNotify ✔ <' + secrets.email.username + '>',
                    to: user.email,
                    subject: show.name + ' returned yesterday - ShowNotify',
                    text: 'Look sharp! ' + show.name + ' returned yesterday with season ' + pad(obj.Data.Episode[0].SeasonNumber, 2) + ' episode ' + pad(obj.Data.Episode[0].EpImgFlag, 2) + ' (s' + pad(obj.Data.Episode[0].SeasonNumber, 2) + 'e' + pad(obj.Data.Episode[0].EpImgFlag, 2) + ') it has been called "' + obj.Data.Episode[0].EpisodeName[0] + '" and this is a short overview: ' + obj.Data.Episode[0].Overview[0],
                    html: 'Look sharp! <br /><br />' +
                      '<strong>' + show.name + '</strong> returned yesterday with season ' + pad(obj.Data.Episode[0].SeasonNumber, 2) + ' episode ' + pad(obj.Data.Episode[0].EpImgFlag, 2) + ' (s' + pad(obj.Data.Episode[0].SeasonNumber, 2) + 'e' + pad(obj.Data.Episode[0].EpImgFlag, 2) + ').<br /><br />' +
                      '<strong>"' + obj.Data.Episode[0].EpisodeName[0] + '"</strong><br />' + obj.Data.Episode[0].Overview[0]
                  };
                  transporter.sendMail(mailOptions, function(error, info){
                    console.log(error, info);
                  });
                });
              });
            }
          });
        });
      }).on('error', function(e) {
        console.error('Got http error: ' + e.message + ''.underline.red);
      });
    });
  }

  setTimeout(function() {
    process.exit(0);
  }, 30000);
});
