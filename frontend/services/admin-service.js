let DashboardUserService = {
  init: function () {
    // BUTTONS
    $(document).off("click", "#btn-get-users").on("click", "#btn-get-users", DashboardUserService.loadAll);
    $(document).off("click", "#btn-get-active-users").on("click", "#btn-get-active-users", DashboardUserService.loadActive);
    $(document).off("click", "#btn-get-inactive-users").on("click", "#btn-get-inactive-users", DashboardUserService.loadInactive);
    $(document).off("click", "#btn-clear-users").on("click", "#btn-clear-users", DashboardUserService.clearTable);
    $(document).off("click", "#btn-clear-json-users").on("click", "#btn-clear-json-users", () => {
      $("#users-result").text("");
    });

    // FORMS
    $(document).off("submit", "#form-get-user-id").on("submit", "#form-get-user-id", e => {
      e.preventDefault();
      const id = $("#user-id").val();
      if (!id) return toastr.error("Enter user ID");
      DashboardUserService.getById(id);
    });

    $(document).off("submit", "#form-create-user").on("submit", "#form-create-user", e => {
      e.preventDefault();
      const payload = {
        first_name: $("#create-user-fn").val(),
        last_name: $("#create-user-ln").val(),
        email: $("#create-user-email").val(),
        password: $("#create-user-password").val(),
        role: $("#create-user-role").val()
      };
      DashboardUserService.create(payload);
    });

    $(document).off("submit", "#form-update-user").on("submit", "#form-update-user", e => {
      e.preventDefault();
      const id = $("#update-user-id").val();
      if (!id) return toastr.error("Enter ID to update");
      const payload = {
        first_name: $("#update-user-fn").val(),
        last_name: $("#update-user-ln").val(),
        email: $("#update-user-email").val(),
        password: $("#update-user-password").val(),
        role: $("#update-user-role").val()
      };
      DashboardUserService.update(id, payload);
    });

    $(document).off("submit", "#form-delete-user").on("submit", "#form-delete-user", e => {
      e.preventDefault();
      const id = $("#delete-user-id").val();
      if (!id) return toastr.error("Enter ID to delete");
      DashboardUserService.remove(id);
    });

    $(document).off("submit", "#form-search-user").on("submit", "#form-search-user", e => {
      e.preventDefault();
      const name = $("#search-user-name").val();
      if (!name) return toastr.error("Enter a name");
      DashboardUserService.searchByName(name);
    });

    $(document).off("submit", "#form-search-user-email").on("submit", "#form-search-user-email", e => {
      e.preventDefault();
      const email = $("#search-user-email").val();
      if (!email) return toastr.error("Enter an email");
      DashboardUserService.searchByEmail(email);
    });

    // ACTIVATE / DEACTIVATE
    $(document).off("click", ".btn-activate").on("click", ".btn-activate", function () {
      DashboardUserService.activate($(this).data("id"));
    });
    $(document).off("click", ".btn-deactivate").on("click", ".btn-deactivate", function () {
      DashboardUserService.deactivate($(this).data("id"));
    });
  },

  // API calls
  loadAll: () => DashboardUserService._fetch("users"),
  loadActive: () => DashboardUserService._fetch("users/active"),
  loadInactive: () => DashboardUserService._fetch("users/inactive"),

  getById: id => DashboardUserService._fetch(`users/${id}`, true),
  create: data => DashboardUserService._mutate("post", "users", data, "User created"),
  update: (id, data) => DashboardUserService._mutate("put", `users/${id}`, data, "User updated"),
  remove: id => DashboardUserService._mutate("delete", `users/${id}`, {}, "User deleted"),
  activate: id => DashboardUserService._mutate("patch", `users/${id}/activate`, {}, "User activated"),
  deactivate: id => DashboardUserService._mutate("patch", `users/${id}/deactivate`, {}, "User deactivated"),

  searchByName: name => DashboardUserService._fetch(`users/search?name=${encodeURIComponent(name)}`),
  searchByEmail: email => DashboardUserService._fetch(`users/by-email?email=${encodeURIComponent(email)}`, true),

  // Generic fetch
  _fetch: (url, single = false) => {
    DashboardUserService._loadingTable(true);
    RestClient.get(url, res => {
      console.log("✅ API RESPONSE from", url, res);

      let list;

      if (single) {
        
        if (res && !Array.isArray(res)) {
          list = [res];
        } else {
          list = res;
        }
      } else {
        // Ako backend vrati false/null → prazna lista
        list = Array.isArray(res) ? res : [];
      }

      DashboardUserService.renderList(list);
      DashboardUserService.showJSON(res);
    }, DashboardUserService.error);
  },

  // Generic mutation (POST, PUT, DELETE, PATCH)
  _mutate: (method, url, data, successMsg) => {
    RestClient[method](url, data, res => {
      toastr.success(successMsg);
      DashboardUserService.loadAll();
      DashboardUserService.showJSON(res);
    }, DashboardUserService.error);
  },

  // Render table
  renderList: list => {
    const $tbody = $("#users-table tbody");
    const $empty = $("#users-empty");
    $tbody.empty();

    if (!list || !list.length) {
      $empty.removeAttr("hidden");
      return;
    }
    $empty.attr("hidden", true);

    const rows = list.map(u => `
      <tr>
        <td>${u.id}</td>
        <td>${u.first_name ?? "-"}</td>
        <td>${u.last_name ?? "-"}</td>
        <td>${u.email ?? "-"}</td>
        <td>${u.role ?? "-"}</td>
        <td>
          ${parseInt(u.is_active) === 1
            ? '<span class="badge green">Active</span>'
            : '<span class="badge gray">Inactive</span>'
          }
        </td>
        <td>
          <button class="btn btn-xs btn-success btn-activate" data-id="${u.id}">Activate</button>
          <button class="btn btn-xs btn-warning btn-deactivate" data-id="${u.id}">Deactivate</button>
        </td>
      </tr>
    `);

    $tbody.html(rows.join(""));
  },

  showJSON: data => $("#users-result").text(JSON.stringify(data ?? {}, null, 2)),

  clearTable: () => {
    $("#users-table tbody").empty();
    $("#users-empty").removeAttr("hidden");
    $("#users-result").text("");
  },

  _loadingTable: isLoading => {
    const $tbody = $("#users-table tbody");
    const $empty = $("#users-empty");
    if (isLoading) {
      $empty.attr("hidden", true);
      $tbody.html(`
        <tr><td colspan="7">
          <div class="empty-state"><span class="spinner"></span> Loading...</div>
        </td></tr>
      `);
    }
  },

  error: xhr => {
    console.error("XHR ERROR", xhr);

    
    if (xhr.status === 200) return;

    const msg = xhr?.responseJSON?.error || "API error";
    $("#users-result").text(JSON.stringify({ error: msg }, null, 2));
    toastr.error(msg);
  }
};

$(document).ready(() => {
  if (window.location.hash === "#dashboard") DashboardUserService.init();
  $(window).on("hashchange", () => {
    if (window.location.hash === "#dashboard") DashboardUserService.init();
  });
});
