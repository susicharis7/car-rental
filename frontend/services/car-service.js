let CarsService = {
  init: function () {
    // filter bar (show cars section)
    $(document).off("click", ".filter-pill").on("click", ".filter-pill", function(){
      if ($(this).is("[disabled]")) return;
      $(".filter-pill").removeClass("active");
      $(this).addClass("active");
      const target = $(this).data("target");
      $(".dash-section").removeClass("show").attr("hidden", true);
      $("#" + target).addClass("show").attr("hidden", false);
    });

    // default focus on cars
    $('.filter-pill[data-target="cars-sec"]').trigger("click");

    // actions
    $(document).off("click", "#btn-get-cars").on("click", "#btn-get-cars", CarsService.loadAll);
    $(document).off("click", "#btn-get-active-cars").on("click", "#btn-get-active-cars", CarsService.loadActive);

    $(document).off("submit", "#form-get-car-id").on("submit", "#form-get-car-id", function(e){
      e.preventDefault();
      CarsService.getById($("#car-id").val());
    });

    $(document).off("submit", "#form-get-available-cars").on("submit", "#form-get-available-cars", function(e){
      e.preventDefault();
      CarsService.getAvailable($("#available-from").val(), $("#available-to").val());
    });

    $(document).off("submit", "#form-create-car").on("submit", "#form-create-car", function(e){
      e.preventDefault();
      CarsService.create({
        model: $("#car-model").val()?.trim(),
        year: Number($("#car-year").val()),
        price_per_day: Number($("#car-price").val())
      });
    });

    // table row actions (delegated)
    $(document).off("click", ".action-edit").on("click", ".action-edit", function(){
      const id = $(this).data("id");
      CarsService.getById(id, true); // true => open modal on load
    });
    $(document).off("click", ".action-toggle").on("click", ".action-toggle", function(){
      const id = $(this).data("id");
      const active = Boolean(Number($(this).data("active"))); // "1" | "0" -> boolean
      CarsService.toggleActive(id, !active);
    });
    $(document).off("click", ".action-delete").on("click", ".action-delete", function(){
      const id = $(this).data("id");
      CarsService.remove(id);
    });

    // modal save & delete
    $(document).off("submit", "#form-update-car").on("submit", "#form-update-car", function(e){
      e.preventDefault();
      CarsService.update($("#update-car-id").val(), {
        model: $("#update-car-model").val()?.trim(),
        year: Number($("#update-car-year").val()),
        price_per_day: Number($("#update-car-price").val())
      });
    });
    $(document).off("click", "#btn-modal-delete").on("click", "#btn-modal-delete", function(){
      const id = $("#update-car-id").val();
      CarsService.remove(id, true);
    });

    // raw json clear
    $(document).off("click", "#btn-clear-json").on("click", "#btn-clear-json", function(){
      $("#cars-result").text("");
    });

    // initial load
    CarsService.loadAll();
  },

  /* ------- API CALLS ------- */
  loadAll: function(){
    CarsService._loadingTable(true);
    RestClient.get("cars", res => {
      CarsService.renderList(Array.isArray(res) ? res : []);
      CarsService.showJSON(res);
    }, CarsService.error);
  },

  loadActive: function(){
    CarsService._loadingTable(true);
    RestClient.get("cars/active", res => {
      CarsService.renderList(Array.isArray(res) ? res : []);
      CarsService.showJSON(res);
    }, CarsService.error);
  },

  getById: function(id, openModal=false){
    if (!id) return toastr.error("Enter Car ID");
    CarsService._loadingTable(true);
    RestClient.get(`cars/${id}`, res => {
      const arr = res ? [res] : [];
      CarsService.renderList(arr);
      CarsService.showJSON(res);
      if (openModal && res) CarsService._prefillModal(res);
    }, CarsService.error);
  },

  getAvailable: function(from, to){
    if (!from || !to) return toastr.error("Enter both dates");
    CarsService._loadingTable(true);
    RestClient.get(`cars/available?from=${from}&to=${to}`, res => {
      CarsService.renderList(Array.isArray(res) ? res : []);
      CarsService.showJSON(res);
    }, CarsService.error);
  },

  create: function(data){
    // basic validation
    if (!data.model || !data.year || !data.price_per_day) {
      return toastr.error("Please fill in all fields.");
    }
    RestClient.post("cars", data, res => {
      toastr.success("Car created");
      $("#form-create-car")[0].reset();
      CarsService.loadAll();
      CarsService.showJSON(res);
    }, CarsService.error);
  },

  update: function(id, data){
    if (!id) return toastr.error("Missing car ID");
    if (!data.model || !data.year || !data.price_per_day) {
      return toastr.error("Fill all fields");
    }
    RestClient.put(`cars/${id}`, data, res => {
      toastr.success("Car updated");
      CarsService.loadAll();
      CarsService.showJSON(res);
      CarsService._closeModal();
    }, CarsService.error);
  },

  toggleActive: function(id, shouldActivate){
    const path = shouldActivate ? `cars/${id}/activate` : `cars/${id}/deactivate`;
    RestClient.patch(path, {}, res => {
      toastr.success(shouldActivate ? "Car activated" : "Car deactivated");
      CarsService.loadAll();
      CarsService.showJSON(res);
    }, CarsService.error);
  },

  remove: function(id, fromModal=false){
    if (!id) return toastr.error("Missing car ID");
    if (!confirm(`Delete car #${id}? This cannot be undone.`)) return;
    RestClient.delete(`cars/${id}`, res => {
      toastr.success(res?.message || "Car deleted");
      CarsService.loadAll();
      CarsService.showJSON(res);
      if (fromModal) CarsService._closeModal();
    }, CarsService.error);
  },

  /* ------- RENDER ------- */
  renderList: function(cars){
    const $tbody = $("#cars-table tbody");
    const $empty = $("#cars-empty");
    $tbody.empty();

    if (!cars || !cars.length){
      $empty.removeAttr("hidden");
      return;
    }
    $empty.attr("hidden", true);

    const rows = cars.map(c => {
      const price = Number(c.price_per_day ?? 0).toFixed(2);
      const active = Number(c.is_active ?? 0) === 1;
      const badge = active ? `<span class="badge green">Active</span>` : `<span class="badge gray">Inactive</span>`;
      const toggleLabel = active ? "Deactivate" : "Activate";

      return `
        <tr>
          <td>${c.id ?? ""}</td>
          <td>${escapeHtml(c.model ?? "")}</td>
          <td>${c.year ?? ""}</td>
          <td>$${price}</td>
          <td>${badge}</td>
          <td class="td-actions">
            <button class="btn btn-info small action-edit" data-id="${c.id}">Edit</button>
            <button class="btn btn-warning small action-toggle" data-id="${c.id}" data-active="${active ? 1 : 0}">${toggleLabel}</button>
            <button class="btn btn-danger small action-delete" data-id="${c.id}">Delete</button>
          </td>
        </tr>
      `;
    }).join("");

    $tbody.html(rows);
  },

  showJSON: function(data){
    $("#cars-result").text(JSON.stringify(data ?? {}, null, 2));
  },

  /* ------- MODAL ------- */
  _prefillModal: function(car){
    $("#update-car-id").val(car.id);
    $("#update-car-model").val(car.model ?? "");
    $("#update-car-year").val(car.year ?? "");
    $("#update-car-price").val(car.price_per_day ?? "");
    const modal = new bootstrap.Modal(document.getElementById("editCarModal"));
    modal.show();
  },
  _closeModal: function(){
    const el = document.getElementById("editCarModal");
    const modal = bootstrap.Modal.getInstance(el);
    if (modal) modal.hide();
  },

  /* ------- UX ------- */
  _loadingTable: function(isLoading){
    const $tbody = $("#cars-table tbody");
    const $empty = $("#cars-empty");
    if (isLoading){
      $empty.attr("hidden", true);
      $tbody.html(`
        <tr><td colspan="6">
          <div class="empty-state"><span class="spinner"></span> Loading...</div>
        </td></tr>
      `);
    }
  },

  /* ------- ERROR ------- */
  error: function(xhr){
    const msg = xhr?.responseJSON?.error || "API error";
    $("#cars-result").text(`{ "error": "${msg}" }`);
    toastr.error(msg);
  }
};

/* small util (avoid XSS in table) */
function escapeHtml(str){
  return String(str).replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[s]));
}

/* INIT on SPA navigation */
$(document).ready(function(){
  if (window.location.hash === "#dashboard") CarsService.init();
  $(window).on("hashchange", function(){
    if (window.location.hash === "#dashboard") CarsService.init();
  });
});
