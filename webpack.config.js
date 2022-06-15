const buildValidations = require('./js/build-utils/build-validations');
const commonConfig = require('./js/build-utils/webpack.common');

const {merge} = require('webpack-merge');

const addons = (/* string | string[] */ addonsArg) => {
  let addons = [...[addonsArg]] // Normalize array of addons (flatten)
    .filter(Boolean); // If addons is undefined, filter it out

  return addons.map(addonName => require(`./js/build-utils/addons/webpack.${addonName}.js`));
};

module.exports = env => {
  if (!env) {
    throw new Error(buildValidations.ERR_NO_ENV_FLAG);
  }

  const envConfig = require(`./js/build-utils/webpack.${process.env.NODE_ENV}.js`);
  const mergedConfig = merge(
    commonConfig,
    envConfig,
    ...addons(env.addons)
  );

  return mergedConfig;
};
