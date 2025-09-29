let ReservationService = {
  init: function () {
    // BUTTONS
    $(document).off("click", "#btn-get-reservations").on("click", "#btn-get-reservations", ReservationService.loadAll);
    $(document).off("click", "#btn-clear-reservations").on("click", "#btn-clear-reservations", ReservationService.clearTable);
    $(document).off("click", "#btn-clear-json-res").on("click", "#btn-clear-json-res", function(){
      $("#reservations-result").text("");
    });

    // FORMS
    $(document).off("submit", "#form-get-reservation-id").on("submit", "#form-get-reservation-id", function(e){
      e.preventDefault();
      const id = $("#reservation-id").val();
      if (!id) return toastr.error("Enter reservation ID");
      ReservationService.getById(id);
    });

    $(document).off("submit", "#form-delete-reservation").on("submit", "#form-delete-reservation", function(e){
      e.preventDefault();
      const id = $("#delete-reservation-id").val();
      if (!id) return toastr.error("Enter ID to delete");
      ReservationService.remove(id);
    });
  },

  loadAll: function () {
    ReservationService._loadingTable(true);
    RestClient.get("reservations", res => {
      ReservationService.renderList(res);
      ReservationService.showJSON(res);
    }, ReservationService.error);
  },

  getById: function (id) {
    ReservationService._loadingTable(true);
    RestClient.get(`reservations/${id}`, res => {
      ReservationService.renderList([res]);
      ReservationService.showJSON(res);
    }, ReservationService.error);
  },

  remove: function (id) {
    if (!confirm(`Delete reservation #${id}?`)) return;
    RestClient.delete(`reservations/${id}`, res => {
      toastr.success("Reservation deleted");
      ReservationService.loadAll();
      ReservationService.showJSON(res);
    }, ReservationService.error);
  },

  renderList: function (list) {
    const $tbody = $("#reservations-table tbody");
    const $empty = $("#reservations-empty");
    $tbody.empty();

    if (!list || !list.length) {
      $empty.removeAttr("hidden");
      return;
    }
    $empty.attr("hidden", true);

    const rows = list.map(r => `
  <tr>
    <td>${r.id}</td>
    <td>${r.user_id}</td>
    <td>${r.car_id}</td>
    <td>${r.pickup_dt}</td>
    <td>${r.return_dt}</td>
    <td>${r.status}</td>
    <td>$${Number(r.total_price ?? 0).toFixed(2)}</td>
  </tr>
`);


    $tbody.html(rows.join(""));
  },

  showJSON: function (data) {
    $("#reservations-result").text(JSON.stringify(data ?? {}, null, 2));
  },

  clearTable: function () {
    $("#reservations-table tbody").empty();
    $("#reservations-empty").removeAttr("hidden");
    $("#reservations-result").text("");
  },

  _loadingTable: function (isLoading) {
    const $tbody = $("#reservations-table tbody");
    const $empty = $("#reservations-empty");
    if (isLoading) {
      $empty.attr("hidden", true);
      $tbody.html(`
        <tr><td colspan="7">
          <div class="empty-state"><span class="spinner"></span> Loading...</div>
        </td></tr>
      `);
    }
  },

  error: function (xhr) {
    const msg = xhr?.responseJSON?.error || "API error";
    $("#reservations-result").text(`{\n  \"error\": \"${msg}\"\n}`);
    toastr.error(msg);
  }
};

$(document).ready(function(){
  if (window.location.hash === "#dashboard") ReservationService.init();
  $(window).on("hashchange", function(){
    if (window.location.hash === "#dashboard") ReservationService.init();
  });
});