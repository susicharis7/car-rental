let RentNowService = { /* here we put functions for rent-now.html */
    init: function () { /* we call it when DOM is ready/section is shown */
        
        /* as soon as we initialize, we call the function that fetches active cars */ 
        RentNowService.loadActiveCars();
        RentNowService.loadLocations();

        /*  
            vezujemo `event listener` na formu s id="rent-form"
            kad user klikne "RENT NOW" pokrece se ovaj callback
            sprijecavamo default ponasanje forme (reload/odlazak na URL `backend/cars/active`)
            i ostajemo na istoj stranici i kasnije mozemo send podatke bez refresha stranice sa AJAXom 
        */
        $(document).on("submit", "#rent-form", function(e){
            e.preventDefault();

            console.log("Form submitted!");

            const formData = {
                car_id: $("#car").val(),
                pickup_location_id: $("#pickup-location").val(),
                return_location_id: $("#return-location").val(),
                pickup_dt: $("#start").val() + " 10:00:00",
                return_dt: $("#end").val() + " 10:00:00"
            };

             console.log("Sending formData:", formData);

             // basic validation
            if (!formData.car_id || !formData.pickup_location_id || !formData.return_location_id || !$("#start").val() || !$("#end").val()) {
                toastr.error("Please fill in all fields.");
                return;
            }

            RestClient.post("reservations", formData, function (response) {
                toastr.success("Reservation created successfully!");
                $("#rent-form")[0].reset();
            }, function (error) {
                console.error("Error response:", error);
                toastr.error(error?.responseJSON?.error || "Reservation failed.");
            });

        });
    },  

    loadActiveCars: function () {
        RestClient.get("cars/active", function (cars) {
            const carSelect = $("#car");
            carSelect.empty(); 
            carSelect.append(`<option value="">Choose Your Car</option>`);

            cars.forEach(car => {
                carSelect.append(`
                    <option value="${car.id}">
                         ${car.model} (${car.year}) - $${car.price_per_day}
                    </option>
                `);
            });
        }, function (error) {
            if (error.status === 401) {
                $(".rent-card").html(`<p class="auth-warning">⚠️ You must register or login to rent a car.</p>`);
            } else {
                console.error("Failed to load active cars.");
            }
        });
    },

    loadLocations: function() {
        RestClient.get("locations", function(locations) {
            const pickupSelect = $("#pickup-location");
            const returnSelect = $("#return-location");

            pickupSelect.empty();
            returnSelect.empty();

            pickupSelect.append(`<option value="">Select Pick-up Location</option>`);
            returnSelect.append(`<option value="">Select Return Location</option>`);

            locations.forEach(location => {
                const option = `<option value="${location.id}">
                ${location.name} (${location.address}, ${location.city}, ${location.country})
                </option>`;

                pickupSelect.append(option);
                returnSelect.append(option);
            })
        }, function(error) {
            if (error.status === 401) {
                $(".rent-card").html(`<p class="auth-warning">⚠️ You must register or login to rent a car.</p>`);
            } else {
                console.error("Failed to load locations.");
            }
        });
    }
};

/* cim se DOM ucita (cim browser zavrsi sa renderovanjem rent-now.html ~ automatski se pokrece RentNowService.init()) */
$(document).ready(function () {
    RentNowService.init();  
});
