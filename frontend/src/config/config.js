const SERVER_VARIABLES = 'codesnip_ai_' 

const getServerVariable = (key, fallback) => {
    if (!key && fallback) return fallback
    if (!(key in SERVER_VARIABLES) || !SERVER_VARIABLES?.[key]) {
      if (import.meta.env.MODE !== 'test') {
        console.error('🚥 Missing server variable:', key)
      }
  
      if (fallback) return fallback
    }
  
    return SERVER_VARIABLES[key]
  }

  const config = {
    AJAX_URL: getServerVariable('ajaxURL', 'http://.local/wp-admin/admin-ajax.php'),
    NONCE: getServerVariable('nonce', ''),
    ROUTE_PREFIX: getServerVariable('routePrefix', 'codesnip_ai_'),
  }

  export default config