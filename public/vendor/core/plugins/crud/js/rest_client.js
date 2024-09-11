/**
  * @desc will hold functions for rest api operations
  * examples include makeRequest(), get(), post(), delete()
  * @required url
*/

let restClient = {};

restClient.makeRequest = function (requestConfig) {
  let defaultConfig = {
    'cache': false
  };
  requestConfig.error = function (jqXHR) {
    if (jqXHR.status == 401) {
      window.location.href = "/auth/logout/";
      return;
    } else if (jqXHR.status == 0) {
      messageUtils.showErrorMessage({
        'message': messageUtils.getErrorMsg("GENERAL_SERVER_NOT_REACHABLE_MSG")
      });
      return;
    }

  };
  requestConfig = $.extend(defaultConfig, requestConfig);
  return $.ajax(requestConfig);
};

restClient.get = function (url, requestConfig) {
  requestConfig = requestConfig || {};
  requestConfig.url = url;
  requestConfig.type = "GET";
  return this.makeRequest(requestConfig);
};

restClient.post = function (url, requestConfig) {
  requestConfig = requestConfig || {};
  requestConfig.url = url;
  requestConfig.type = "POST";
  return this.makeRequest(requestConfig);
};

restClient.delete = function (url, requestConfig) {
  requestConfig = requestConfig || {};
  requestConfig.url = url;
  requestConfig.type = "DELETE";
  return this.makeRequest(requestConfig);
};

restClient.post = function (url, requestConfig) {
  requestConfig = requestConfig || {};
  requestConfig.url = url;
  requestConfig.type = "POST";
  return this.makeRequest(requestConfig);
};

restClient.put = function (url, requestConfig) {
  requestConfig = requestConfig || {};
  requestConfig.url = url;
  requestConfig.type = "PUT";
  return this.makeRequest(requestConfig);
};
