[![endorse](http://api.coderwall.com/deviavir/endorsecount.png)](http://coderwall.com/deviavir)

# ShowNotify
This node project allows users to be notified of new shows.

### config/secrets.js

```
module.exports = {
  db: process.env.MONGODB|| 'mongodb://localhost:27017/shownotify',

  sessionSecret: process.env.SESSION_SECRET || '%YOUR_SESSION_SECRET%',

  sendgrid: {
    user: process.env.SENDGRID_USER || 'username',
    password: process.env.SENDGRID_PASSWORD || 'password'
  },

  facebook: {
    clientID: process.env.FACEBOOK_ID || 'id',
    clientSecret: process.env.FACEBOOK_SECRET || 'secret',
    callbackURL: '/auth/facebook/callback',
    passReqToCallback: true
  },

  github: {
    clientID: process.env.GITHUB_ID || 'id',
    clientSecret: process.env.GITHUB_SECRET || 'secret',
    callbackURL: '/auth/github/callback',
    passReqToCallback: true
  },

  twitter: {
    consumerKey: process.env.TWITTER_KEY || 'key',
    consumerSecret: process.env.TWITTER_SECRET  || 'secret',
    callbackURL: '/auth/twitter/callback',
    passReqToCallback: true
  },

  google: {
    clientID: process.env.GOOGLE_ID || 'id',
    clientSecret: process.env.GOOGLE_SECRET || 'secret',
    callbackURL: '/auth/google/callback',
    passReqToCallback: true
  },

  thetvdb: {
    apiKey: process.env.THETVDB_KEY || 'key'
  }
};
```