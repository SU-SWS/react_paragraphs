export const UrlFix = (path) => {
  const domain = typeof process.env.DRUPAL_DOMAIN !== 'undefined' ? process.env.DRUPAL_DOMAIN.replace(/\/$/, '') : '';
  return domain + path;
};
