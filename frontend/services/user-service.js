let UserService = {
  init: function () {

    // LOGIN
    $("#login-form").validate({
      submitHandler: function (form, event) {
        event.preventDefault();

        let formData = {
          email: $(form).find("input[type='email']").val(),
          password: $(form).find("input[type='password']").val()
        };

        RestClient.post("auth/login", formData, function (response) {
          const token = response?.data?.token;

          if (token) {
            localStorage.setItem("user_token", token);
            localStorage.setItem("user_email", response.data.email);
            localStorage.setItem("user_role", response.data.role);

            toastr.success("Login successful!");
            window.location.href = "#home";
            UserService.generateMenuItems();
          } else {
            toastr.error("Login failed: token not received.");
          }
        }, function (error) {
          toastr.error(error?.responseJSON?.error || "Invalid login credentials");
        });
      }
    });

    // REGISTER
     $(document).on("submit", "#register-form", function (e) {
      e.preventDefault();

      const form = this;
      const inputs = $(form).find("input");

      const formData = {
        first_name: inputs.eq(0).val(),
        last_name: inputs.eq(1).val(),
        email: inputs.eq(2).val(),
        password: inputs.eq(3).val(),
        confirm_password: inputs.eq(4).val(),
        role: "user"
      };

      if (formData.password !== formData.confirm_password) {
        toastr.error("Passwords do not match.");
        return;
      }

      delete formData.confirm_password;

      console.log("Submitting registration with data:", formData);

      RestClient.post("auth/register", formData, function (response) {
        toastr.success("Registration successful.");
        window.location.href = "#login";
      }, function (error) {
        toastr.error(error?.responseJSON?.error || "Registration failed.");
      });
    });

    // LOGOUT
    $(document).on("click", "#logout-link", function (e) {
      e.preventDefault();
      localStorage.removeItem("user_token");
      localStorage.removeItem("user_email");
      localStorage.removeItem("user_role");
      toastr.success("You have been logged out.");
      window.location.href = "#login";
      UserService.generateMenuItems(); 
    });
  },

  generateMenuItems: function () {
  const token = localStorage.getItem("user_token");
  const userRole = localStorage.getItem("user_role");

  const navMenu = $("#nav-menu");

  let navItems = `
    <li><a href="#home">Home</a></li>
    <li><a href="#cars">List Of Cars</a></li>
    <li><a href="#rent-now">Rent Now</a></li>
  `;

  if (!token) {
    // ako nije logovan, prikazi login + register
    navItems += `
      <li><a href="#register">Register</a></li>
      <li><a href="#login">Login</a></li>
    `;
  } else {
    // ako jeste logovan, prikazi profile + logout
    navItems += `
      <li><a href="#profile">My Profile</a></li>
      <li><a href="#login" id="logout-link">Logout</a></li>
    `;

    // ako je admin, dodaj i dashboard
    if (userRole === "admin") {
      navItems += `<li><a href="#dashboard">Dashboard</a></li>`;
    }
  }

  navMenu.html(navItems);
}

};
