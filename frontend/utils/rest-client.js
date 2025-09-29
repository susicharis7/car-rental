let RestClient = {
    /*
      ~ uses `$.ajax()` to send GET Request on Constants.PROJECT_BASE_URL + url >> localhost/car-rental/backend/ + `url` (can be /cars , /cars/id ect...)   
      ~ it adds `Authentication Header` with JWT Token from `localStorage`
      ~ if it succeeds :: callback(response) ~ if it doesn't error_callback(jqXHR)
    */
    get: function(url, callback, error_callback) {
        $.ajax({
            url: Constants.PROJECT_BASE_URL + url,
            type: "GET",
            beforeSend: function (xhr) {
                xhr.setRequestHeader(
                    "Authentication",
                    localStorage.getItem("user_token")
                );
            },

            success: function (response) {
                if (callback) callback(response);
            },

            error: function (jqXHR, textStatus, errorThrown) {
                if (error_callback) error_callback(jqXHR);
            },
        });
    },


    /*
      ~ main generic function that is being used by `post, put, patch, delete`
      ~ it also sets the Authentication Header (from localStorage)
        url (ex. `cars/5`)
        method : POST, PUT, PATCH, DELETE
        data : data that we send 
        callback : if request succeeds 
    */
    request: function(url, method, data, callback, error_callback) {
        $.ajax({
            url: Constants.PROJECT_BASE_URL + url,
            type: method,
            beforeSend: function (xhr) {
                const noAuthNeeded = url.includes("auth/login") || url.includes("auth/register");
                const token = localStorage.getItem("user_token");

                if (!noAuthNeeded && token) {
                    xhr.setRequestHeader("Authentication", token);
                }
            },

            data: data,
        }) .done(function (response,status, jqXHR) {
                if (callback) callback(response);
        }) .fail(function (jqXHR, textStatus, errorThrown) {
            if (error_callback) {
                error_callback(jqXHR);
            } else {
                toastr.error(jqXHR.responseJSON.message);
            }
        });
    },

    post: function(url, data, callback, error_callback) {
        RestClient.request(url, "POST", data, callback, error_callback);
    },

    delete: function(url, data, callback, error_callback) {
        RestClient.request(url, "DELETE", data, callback, error_callback);
    },

    patch: function(url, data, callback, error_callback) {
        RestClient.request(url, "PATCH", data, callback, error_callback);
    },

    put: function(url, data, callback, error_callback) {
        RestClient.request(url, "PUT", data, callback, error_callback);
    },

};