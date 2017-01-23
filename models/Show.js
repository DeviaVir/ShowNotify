var mongoose = require('mongoose');

var showSchema = new mongoose.Schema({
  id: { type: Number, unique: true, lowercase: true },
  showId: { type: Number, unique: true, lowercase: true },
  name: String,
  users: Array
});

module.exports = mongoose.model('Show', showSchema);
