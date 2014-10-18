var mongoose = require('mongoose');

var showSchema = new mongoose.Schema({
  id: { type: Integer, unique: true, lowercase: true }
});

module.exports = mongoose.model('Show', showSchema);
