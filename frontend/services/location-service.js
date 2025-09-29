let LocationService = {
  init: function () {
    // BUTTONS
    $(document).off("click", "#btn-get-locations").on("click", "#btn-get-locations", LocationService.loadAll);
    $(document).off("click", "#btn-clear-locations").on("click", "#btn-clear-locations", LocationService.clearTable);
    $(document).off("click", "#btn-clear-json-loc").on("click", "#btn-clear-json-loc", function(){
      $("#locations-result").text("");
    });

    // FORMS
    $(document).off("submit", "#form-get-location-id").on("submit", "#form-get-location-id", function(e){
      e.preventDefault();
      const id = $("#location-id").val();
      if (!id) return toastr.error("Enter ID");
      LocationService.getById(id);
    });

    $(document).off("submit", "#form-search-location").on("submit", "#form-search-location", function(e){
      e.preventDefault();
      const city = $("#search-city").val()?.trim();
      const country = $("#search-country").val()?.trim();
      LocationService.search(city, country);
    });

    $(document).off("submit", "#form-create-location").on("submit", "#form-create-location", function(e){
      e.preventDefault();
      const data = {
        name: $("#location-name").val()?.trim(),
        address: $("#location-address").val()?.trim(),
        city: $("#location-city").val()?.trim(),
        country: $("#location-country").val()?.trim()
      };
      LocationService.create(data);
    });

    $(document).off("submit", "#form-delete-location").on("submit", "#form-delete-location", function(e){
      e.preventDefault();
      const id = $("#delete-location-id").val();
      if (!id) return toastr.error("Enter ID to delete");
      LocationService.remove(id);
    });
  },

  loadAll: function () {
    LocationService._loadingTable(true);
    RestClient.get("locations", res => {
      LocationService.renderList(res);
      LocationService.showJSON(res);
    }, LocationService.error);
  },

  getById: function (id) {
    LocationService._loadingTable(true);
    RestClient.get(`locations/${id}`, res => {
      LocationService.renderList([res]);
      LocationService.showJSON(res);
    }, LocationService.error);
  },

  search: function (city, country) {
    if (!city && !country) return toastr.error("Enter city or country");
    let url = "locations/search";
    if (city) url += `?city=${encodeURIComponent(city)}`;
    else if (country) url += `?country=${encodeURIComponent(country)}`;

    LocationService._loadingTable(true);
    RestClient.get(url, res => {
      LocationService.renderList(res);
      LocationService.showJSON(res);
    }, LocationService.error);
  },

  create: function (data) {
    if (!data.name || !data.address || !data.city || !data.country)
      return toastr.error("All fields required");

    RestClient.post("locations", data, res => {
      toastr.success("Location created");
      $("#form-create-location")[0].reset();
      LocationService.loadAll();
      LocationService.showJSON(res);
    }, LocationService.error);
  },

  remove: function (id) {
    if (!confirm(`Delete location #${id}?`)) return;
    RestClient.delete(`locations/${id}`, res => {
      toastr.success("Location deleted");
      LocationService.loadAll();
      LocationService.showJSON(res);
    }, LocationService.error);
  },

  renderList: function (list) {
    const $tbody = $("#locations-table tbody");
    const $empty = $("#locations-empty");
    $tbody.empty();

    if (!list || !list.length) {
      $empty.removeAttr("hidden");
      return;
    }
    $empty.attr("hidden", true);

    const rows = list.map(loc => `
      <tr>
        <td>${loc.id}</td>
        <td>${escapeHtml(loc.name)}</td>
        <td>${escapeHtml(loc.address)}</td>
        <td>${escapeHtml(loc.city)}</td>
        <td>${escapeHtml(loc.country)}</td>
      </tr>
    `);

    $tbody.html(rows.join(""));
  },

  showJSON: function (data) {
    $("#locations-result").text(JSON.stringify(data ?? {}, null, 2));
  },

  clearTable: function () {
    $("#locations-table tbody").empty();
    $("#locations-empty").removeAttr("hidden");
    $("#locations-result").text("");
  },

  _loadingTable: function (isLoading) {
    const $tbody = $("#locations-table tbody");
    const $empty = $("#locations-empty");
    if (isLoading) {
      $empty.attr("hidden", true);
      $tbody.html(`
        <tr><td colspan="5">
          <div class="empty-state"><span class="spinner"></span> Loading...</div>
        </td></tr>
      `);
    }
  },

  error: function (xhr) {
    const msg = xhr?.responseJSON?.error || "API error";
    $("#locations-result").text(`{\n  \"error\": \"${msg}\"\n}`);
    toastr.error(msg);
  }
};

$(document).ready(function(){
  if (window.location.hash === "#dashboard") LocationService.init();
  $(window).on("hashchange", function(){
    if (window.location.hash === "#dashboard") LocationService.init();
  });
});
