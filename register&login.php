<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Event Managment</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
      crossorigin="anonymous"
    />
    <script
      src="https://kit.fontawesome.com/9291c1d06a.js"
      crossorigin="anonymous"
    ></script>
  </head>
  <body class="bg-body-tertiary">
    <section class="container d-flex flex-column mt-5">
      <!-- Pills navs -->
      <ul class="nav nav-pills nav-justified mb-3 mt-5" id="ex1" role="tablist">
        <li class="nav-item" role="presentation">
          <a
            class="nav-link active"
            id="tab-login"
            data-mdb-pill-init
            href="#pills-login"
            role="tab"
            aria-controls="pills-login"
            aria-selected="true"
            >Login</a
          >
        </li>
        <li class="nav-item" role="presentation">
          <a
            class="nav-link"
            id="tab-register"
            data-mdb-pill-init
            href="#pills-register"
            role="tab"
            aria-controls="pills-register"
            aria-selected="false"
            >Register</a
          >
        </li>
      </ul>
      <!-- Pills navs -->

      <!-- Pills content -->
      <div class="tab-content">
        <div
          class="tab-pane fade show active"
          id="pills-login"
          role="tabpanel"
          aria-labelledby="tab-login"
        >
          <form action="backend/login.php"  method="post">
            <!-- Email input -->
            <div data-mdb-input-init class="form-outline mb-4">
              <input
                type="email"
                id="email"
                name="email"
                class="form-control"
              />
              <label class="form-label" for="email">Email </label>
            </div>

            <!-- Password input -->
            <div data-mdb-input-init class="form-outline mb-4">
              <input type="password" id="loginPassword" name="loginPassword" class="form-control" />
              <label class="form-label" for="loginPassword">Password</label>
            </div>

            <!-- Submit button -->
            <div class="text-center">
              <input
                value="Sign in"
                type="submit"
                data-mdb-button-init
                data-mdb-ripple-init
                class="btn btn-primary btn-block text-center mb-4"
              >
                
              </input>
            </div>
            <!-- Register buttons -->
            <div class="text-center">
              <p>Not a member? <button class="btn btn-secondary text-center " onclick='showTab("register")'>Register</button></p>
            </div>
          </form>
        </div>
        <div
          class="tab-pane fade"
          id="pills-register"
          role="tabpanel"
          aria-labelledby="tab-register"
        >
        <form action="backend/register.php" method="POST">
 
            <!-- Name input -->   
            <div data-mdb-input-init class="form-outline mb-4">
              <input type="text" id="registerName" name="registerName" class="form-control" required/>
              <label class="form-label" for="registerName">Full Name</label>
            </div>

           
            <!-- Email input -->
            <div data-mdb-input-init class="form-outline mb-4">
              <input type="email" id="registerEmail" name="registerEmail" class="form-control" required />
              <label class="form-label" for="registerEmail">Email</label>
            </div>

            <!-- Password input -->
            <div data-mdb-input-init class="form-outline mb-4">
              <input
                type="password"
                name="registerPassword"
                id="registerPassword"
                class="form-control"
                required
              />
              <label class="form-label" for="registerPassword">Password</label>
            </div>
            <div class="">
              <select class="form-select form-select-lg mb-3" aria-label="Large select example" name="userType">
                <option value="" disabled selected>User Typer</option>
                <option value="regular">Normal browser</option>
                <option value="organizer">Event organizer</option>
                <option value="admin">Admin</option>
              </select>
            </div>
           
            <!-- Checkbox -->
            <div class="form-check d-flex justify-content-center mb-4">
              <input
                class="form-check-input me-2"
                type="checkbox"
                value=""
                id="registerCheck"
                checked
                aria-describedby="registerCheckHelpText"
              />
              <label class="form-check-label" for="registerCheck">
                I have read and agree to the <a href="https://quizizz.com/admin/quiz/5e9fbd75be0213001b263b00/mango-quiz" target="_blank"> terms </a>
              </label>
            </div>

            <!-- Submit button -->
            <input
              type="submit"
              data-mdb-button-init
              data-mdb-ripple-init
              value="Sign in"
              class="btn btn-primary btn-block mb-3"
            >
            </input>
          </form>
        </div>
      </div>
      <!-- Pills content -->
    </section>

    <script src="utils/login switch.js"></script>

    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"
    ></script>
  </body>
</html>
