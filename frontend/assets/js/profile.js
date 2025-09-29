let selectedReservationId = null; /* for modal */

let ProfileService = {
    init: function() {
        ProfileService.loadUser();
        ProfileService.loadReservations();
        ProfileService.bindModalEvents()
    },

    loadUser: function() {
        RestClient.get("users/me", function(user) {
            $("#first-name").text(user.first_name);
            $("#last-name").text(user.last_name);
            $("#email").text(user.email);
            $("#role").text(user.role);

            const fullName = `${user.first_name} ${user.last_name}`;
            $("#full-name").text(fullName);
        }, function(error) {
            toastr.error("Failed to load user profile.");
            console.error(error);
        });
    },

    loadReservations: function() {
        RestClient.get("reservations/mine", function(reservations) {
            const container = $("#my-reservations");
            container.empty();

            if(reservations.length === 0) {
                container.append(`<p style="text-align:center; color:#777;">You have no reservations.</p>`);
                return;
            }

            reservations.forEach(res => {
                const box = `
                <div class="reservation-box" data-id="${res.id}">
                    <div><strong>Car:</strong> ${res.model || 'Unknown'}</div>
                    <div><strong>Pickup:</strong> ${res.pickup_dt}</div>
                    <div><strong>Return:</strong> ${res.return_dt}</div>
                    <div><strong>Status:</strong> <span class="status-tag status-${res.status.toLowerCase()}">${res.status}</span></div>
                    ${
                        res.status === "CONFIRMED" || res.status === "PENDING"
                        ? `<button class="cancel-btn">Cancel</button>`
                        : ""
                    }
                </div>
                `;

                container.append(box);
            });

            ProfileService.bindCancelButtons();
        }, function(error) {
            toastr.error("Failed to load reservations.");
            console.error(error);
        });
    },

    bindCancelButtons: function() {
        $(".cancel-btn").on("click", function() {
            const box = $(this).closest(".reservation-box");
            selectedReservationId = box.data("id");

            if (!selectedReservationId) return;

            $("#cancel-modal").fadeIn();
        });
    },

    bindModalEvents: function () {
    $(document).on("click", "#confirm-cancel", function () {
        if (!selectedReservationId) return;

        RestClient.put(`reservations/${selectedReservationId}/cancel`, {}, function () {
            toastr.success("Reservation cancelled successfully.");
            ProfileService.loadReservations();
        }, function (error) {
            toastr.error("Failed to cancel reservation.");
            console.error(error);
        });

        $("#cancel-modal").fadeOut();
        selectedReservationId = null;
    });

    
    $(document).on("click", "#close-modal", function () {
        $("#cancel-modal").fadeOut();
        selectedReservationId = null;
    });

}

};

$(document).ready(function () {
    ProfileService.init();
});
