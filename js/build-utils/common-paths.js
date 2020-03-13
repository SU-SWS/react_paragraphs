const path = require('path');
const PROJECT_ROOT = path.resolve(__dirname, '../');

module.exports = {
  projectRoot: PROJECT_ROOT,
  outputPath: path.join(PROJECT_ROOT, 'build'),
  appEntry: path.join(PROJECT_ROOT, 'src'),
  drupalEntry: path.join(PROJECT_ROOT, '../scss')
};
