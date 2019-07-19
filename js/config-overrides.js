/* config-overrides.js */

module.exports = function override(config, env) {
  if (env === 'production') {
    config.output.filename = '[name].js';
    config.output.chunkFilename = '[name].chunk.js';
  }

  return config;
};
