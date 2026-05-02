<?php
    session_start();

    require_once "./database.php";
    $db = new Database;

    if (!isset($_SESSION["email"])) {
        header("Location: login.php");
        exit;
    }

    

    $user = $db->getUserByEmail($_SESSION["email"]);
    $userId = $user["id"];
    $appointments = $db->getAppointmentsByUserId($userId);

    if (!isset($_SESSION["stage"]) || $_SESSION["stage"] < 1 || $_SESSION["stage"] > 4) {
        $_SESSION["stage"] = 1;
        
    }

    if(!isset($_SESSION["timeSlot"])){
        $_SESSION["timeSlot"] = [
           "09:00 AM", "10:00 AM", "11:00 AM", "12:00 PM",
            "1:00 PM", "2:00 PM", "3:00 PM", "4:00 PM"
        ];

     }

    

    if (isset($_POST["action"])) {

        

        if ($_POST["action"] === "back") {
            $_SESSION["stage"] = max(1, $_SESSION["stage"] - 1);
        }

        elseif ($_POST["action"] === "next") {

            $valid = true;
            $error = "";

            if ($_SESSION["stage"] == 2) {
                if (empty($_POST["stylist"]) || empty($_POST["services"])) {
                    $error =  "Select a stylist and at least one service";
                    $valid = false;
                } else {

                    $stylistId = $db->getStylistId($_POST["stylist"]);

                    if (!$stylistId) {
                        $error = "Invalid stylist selected";
                        $valid = false;
                    } else {
                        $_SESSION["stylist"] = $_POST["stylist"];
                        $_SESSION["services"] = $_POST["services"];
                    }
                }
            }

            elseif ($_SESSION["stage"] == 3) {


                if (empty($_POST["date"]) || !isset($_POST["selectedTime"])) {
                    $error = "Please pick a date and time";
                    $valid = false;
                } elseif(strtotime($_POST["date"]) < strtotime(date("Y-m-d"))){
                    $error = "Please pick a future date";
                    $valid = false;                    
                }else{
                    $_SESSION["date"] = $_POST["date"];
                    $_SESSION["pickedTimeSlot"] = $_POST["selectedTime"];
                }
            }

            elseif($_SESSION["stage"] == 4){

                $user = $db->getUserByEmail($_SESSION["email"]);
                $userId = $user["id"];
                $stylistId = $db->getStylistId($_SESSION["stylist"]);
                $timeSlot = $_SESSION["pickedTimeSlot"];
                $date = $_SESSION["date"];

                $bookedSlots = $db->getBookedTimeSlots($stylistId);

                $conflict = false;

                foreach ($bookedSlots as $slot) {
                    if ($slot["date"] === $date && $slot["time_slot"] === $timeSlot) {
                        $conflict = true;
                        break;
                    }
                }

                if ($conflict) {
                    $_SESSION["stage"] = 3;
                    $error = "This time slot is already booked. Please choose another.";
                    $valid = false;
                } else {
                    $db->setAppointment($userId, $stylistId, $timeSlot, $date);

                    unset(
                        $_SESSION["stylist"],
                        $_SESSION["services"],
                        $_SESSION["date"],
                        $_SESSION["pickedTimeSlot"],
                        $_SESSION["timeSlot"]
                    );


                    $_SESSION["confirmed"] = "<script>alert('Appointment Booked!');</script>";

                    $_SESSION["stage"] = 1;

                    header("Location: index.php");
                    exit;
                }
            }


            if ($valid) {

                $_SESSION["stage"]++;
                
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/Styles/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Welcome Page</title>
</head>
<body>
    <style>

            body{
                background-image: url('./Styles/marbleback.jpg');
                background-size: cover;
                background-repeat: repeat;
                background-position: center;
    
            }

            body::before {
                content: "";
                position: fixed;
                inset: 0; /* top:0; left:0; right:0; bottom:0 */
                
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);

                background: rgba(255, 255, 255, 0.1); /* helps the blur show */
                
                z-index: -1;
            }

            #indexCDiv{
                display: inline-flex;
                flex-direction: column;

                position: relative;

            }

            #indexCDiv h1{
                display: inline-flex;
                justify-content: center; 
                align-items: center; 
                /*top: -80px;*/
            }

            #stageBtn{
                position:fixed;

                top: 65%;
                /*left: 45%;*/
            }


        </style>

    <nav class="navbar navbar-expand-lg navbar-light bg-light" style="width:100%; position:absolute; z-index: 1;">

        <h2 class="navbar-brand" style="padding-left:20px; padding-right:20px;">Appointment App</h2>
    
           
        <a href="logout.php" class="btn btn-outline-success my-2 my-sm-0">Logout</a>

    </nav>

    <?php if($_SESSION["stage"] == 1 || $_SESSION["stage"] == 5): ?>

                  
            <div class="container d-flex flex-column align-items-center justify-content-center">

                <?php if (isset($_SESSION["confirmed"])): ?>
                    <?= $_SESSION["confirmed"]; ?>

                    <?php unset($_SESSION["confirmed"]); ?>
                <?php endif; ?>

                <h1 class="mb-4">Welcome <?= htmlspecialchars($_SESSION["fullName"]) ?>!</h1>

                <div class="card shadow-sm p-3 w-100" style="max-width: 800px;">
                    <h3 class="mb-3">Your Appointments</h3>

                    <?php if (!empty($appointments)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Stylist</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($appointments as $a): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($a["date"]) ?></td>
                                            <td><?= htmlspecialchars($a["time_slot"]) ?></td>
                                            <td><?= htmlspecialchars($a["stylist"]) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No appointments booked yet.</p>
                    <?php endif; ?>
                </div>

                <!-- ACTION BUTTON -->
                <form method="POST" class="mt-4">
                    <button type="submit"
                            name="action"
                            value="next"
                            class="btn btn-outline-success">
                        Book Appointment
                    </button>
                </form>

            </div>
       

    <?php endif; ?>

    <?php if($_SESSION["stage"] == 2): ?>

        <form method="POST" id="backForm" style="width: 100%;" class="container text-center">

            <div class="container">
                <div class="container-sm" id="selectService">
                    <label style="padding-bottom: 10px;"><strong><h4>Select Services</h4></strong></label>
            
                        <!--Shampoo & Mask-->
                        <label class="cosmic-checkbox">
                            <input type="checkbox" name="services[]" value="Shampoo & Mask"/>
                            <div class="checkbox-container">
                                <div class="checkbox-box">
                                <div class="checkbox-bg"></div>

                                <svg class="checkmark" viewBox="0 0 24 24" fill="none">
                                    <path
                                    d="M4 12.5L9.5 18L20 6"
                                    stroke="currentColor"
                                    stroke-width="2.5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    ></path>
                                </svg>

                                <div class="particle p1"></div>
                                <div class="particle p2"></div>
                                <div class="particle p3"></div>
                                <div class="particle p4"></div>
                                <div class="particle p5"></div>
                                <div class="particle p6"></div>

                                <div class="ring ring-1"></div>
                                <div class="ring ring-2"></div>
                                <div class="ring ring-3"></div>

                                <div class="spark s1"></div>
                                <div class="spark s2"></div>
                                <div class="spark s3"></div>
                                <div class="spark s4"></div>
                                <div class="spark s5"></div>
                                <div class="spark s6"></div>
                                <div class="spark s7"></div>
                                <div class="spark s8"></div>
                                </div>
                                <span class="label-text">Shampoo & Mask</span>
                            </div>
                        </label>
                    
                        <!--Shampoo & Blowdry-->
                        <label class="cosmic-checkbox">
                            <input type="checkbox" name="services[]" value="Shampoo & Blowdry"/>
                            <div class="checkbox-container">
                                <div class="checkbox-box">
                                <div class="checkbox-bg"></div>

                                <svg class="checkmark" viewBox="0 0 24 24" fill="none">
                                    <path
                                    d="M4 12.5L9.5 18L20 6"
                                    stroke="currentColor"
                                    stroke-width="2.5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    ></path>
                                </svg>

                                <div class="particle p1"></div>
                                <div class="particle p2"></div>
                                <div class="particle p3"></div>
                                <div class="particle p4"></div>
                                <div class="particle p5"></div>
                                <div class="particle p6"></div>

                                <div class="ring ring-1"></div>
                                <div class="ring ring-2"></div>
                                <div class="ring ring-3"></div>

                                <div class="spark s1"></div>
                                <div class="spark s2"></div>
                                <div class="spark s3"></div>
                                <div class="spark s4"></div>
                                <div class="spark s5"></div>
                                <div class="spark s6"></div>
                                <div class="spark s7"></div>
                                <div class="spark s8"></div>
                                </div>
                                <span class="label-text">Shampoo & Blowdry</span>
                            </div>
                        </label>
                    

                        <!--Highlights-->
                        <label class="cosmic-checkbox">
                            <input type="checkbox" name="services[]" value="Highlights"/>
                            <div class="checkbox-container">
                                <div class="checkbox-box">
                                <div class="checkbox-bg"></div>

                                <svg class="checkmark" viewBox="0 0 24 24" fill="none">
                                    <path
                                    d="M4 12.5L9.5 18L20 6"
                                    stroke="currentColor"
                                    stroke-width="2.5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    ></path>
                                </svg>

                                <div class="particle p1"></div>
                                <div class="particle p2"></div>
                                <div class="particle p3"></div>
                                <div class="particle p4"></div>
                                <div class="particle p5"></div>
                                <div class="particle p6"></div>

                                <div class="ring ring-1"></div>
                                <div class="ring ring-2"></div>
                                <div class="ring ring-3"></div>

                                <div class="spark s1"></div>
                                <div class="spark s2"></div>
                                <div class="spark s3"></div>
                                <div class="spark s4"></div>
                                <div class="spark s5"></div>
                                <div class="spark s6"></div>
                                <div class="spark s7"></div>
                                <div class="spark s8"></div>
                                </div>
                                <span class="label-text">Highlights</span>
                            </div>
                        </label>
                    

                        <!--Hair Extensions-->
                        <label class="cosmic-checkbox">
                            <input type="checkbox" name="services[]" value="Hair Extensions"/>
                            <div class="checkbox-container">
                                <div class="checkbox-box">
                                <div class="checkbox-bg"></div>

                                <svg class="checkmark" viewBox="0 0 24 24" fill="none">
                                    <path
                                    d="M4 12.5L9.5 18L20 6"
                                    stroke="currentColor"
                                    stroke-width="2.5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    ></path>
                                </svg>

                                <div class="particle p1"></div>
                                <div class="particle p2"></div>
                                <div class="particle p3"></div>
                                <div class="particle p4"></div>
                                <div class="particle p5"></div>
                                <div class="particle p6"></div>

                                <div class="ring ring-1"></div>
                                <div class="ring ring-2"></div>
                                <div class="ring ring-3"></div>

                                <div class="spark s1"></div>
                                <div class="spark s2"></div>
                                <div class="spark s3"></div>
                                <div class="spark s4"></div>
                                <div class="spark s5"></div>
                                <div class="spark s6"></div>
                                <div class="spark s7"></div>
                                <div class="spark s8"></div>
                                </div>
                                <span class="label-text">Hair Extensions</span>
                            </div>
                        </label>
                    

                        <!--Balayage-->
                        <label class="cosmic-checkbox">
                            <input type="checkbox" name="services[]" value="Balayage"/>
                            <div class="checkbox-container">
                                <div class="checkbox-box">
                                <div class="checkbox-bg"></div>

                                <svg class="checkmark" viewBox="0 0 24 24" fill="none">
                                    <path
                                    d="M4 12.5L9.5 18L20 6"
                                    stroke="currentColor"
                                    stroke-width="2.5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    ></path>
                                </svg>

                                <div class="particle p1"></div>
                                <div class="particle p2"></div>
                                <div class="particle p3"></div>
                                <div class="particle p4"></div>
                                <div class="particle p5"></div>
                                <div class="particle p6"></div>

                                <div class="ring ring-1"></div>
                                <div class="ring ring-2"></div>
                                <div class="ring ring-3"></div>

                                <div class="spark s1"></div>
                                <div class="spark s2"></div>
                                <div class="spark s3"></div>
                                <div class="spark s4"></div>
                                <div class="spark s5"></div>
                                <div class="spark s6"></div>
                                <div class="spark s7"></div>
                                <div class="spark s8"></div>
                                </div>
                                <span class="label-text">Balayage</span>
                            </div>
                        </label>
                        <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
                </div>
            </div>

            <div class="container">
                <div class="container-sm" id="chooseStylist">
                    <label><h4 style="padding-bottom: 20px;">Choose a Stylist</h4></label>

                    <img id="stylistImage">
                    <br>
                    <select name="stylist" id="stylist" class="form-select" aria-label="Default select example" style="position:relative;">
                        <option disabled selected>Choose Stylist</option>
                        <option value="Alondra">Alondra</option>
                        <option value="Henry">Henry</option>
                        <option value="Christine">Christine</option>
                        <option value="Sally">Sally</option>
                    </select>
                    <br>
                    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

                    <button type="submit" name="action" value="back" class="btn btn-secondary" id="goback">Go Back</button>
                    <button type="submit" name="action" value="next" class="btn btn-primary" id="next">Next</button>
                </div>
            </div>
                
        </form>

    <?php endif; ?>
    
    <?php if($_SESSION["stage"] == 3): ?>

        <div class="container" id="indexCDiv">
            <h1>choose a Time! </h1>
            <form method="POST" id="stageBtn" style="display: inline-flex; flex-direction: column; position: relative; top: 50px;">

                <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

                <input type="date" name="date" id="dateInput" class="form-control"/>
                <br>

                <div class="radio-input">
                    
                    <?php foreach (($_SESSION["timeSlot"]) as $index => $slot): ?> 
                        <input value="<?= htmlspecialchars($slot) ?>" name="selectedTime" id="value-<?= $index ?>" type="radio" required /> 
                        <label for="value-<?= $index ?>"> 
                            <div class="text"> 
                                <span class="circle"></span> <?= htmlspecialchars($slot) ?> 
                            </div> 
                        </label> 
                    <?php endforeach; ?>
                    
                </div>
                <br>

                <div style="padding-left: 20px; width: 100%; justify-content:center;">
                    <button type="submit" name="action" value="back" class="btn btn-secondary" id="goback">Go Back</button>
                    <button type="submit" name="action" value="next" class="btn btn-primary" id="next">Next</button>
                </div>
            </form>
        </div>

    <?php endif; ?>

    <?php if($_SESSION["stage"] == 4): ?>
            <form method="POST" id="checkout">                 
                <div class="container" style="flex-direction: column;">

                    <div class="card cart">
                        <span style="font-size: 15px; font-weight: 600;">APPOINTMENT CONFIRMATION</span>
                        <div class="steps">
                            <div class="step">
                                <div>
                                    <span><?= htmlspecialchars($_SESSION["fullName"]) ?></span>
                                    <p><?= htmlspecialchars($_SESSION["email"]) ?></p>
                                    <p>701 N Econlockhatchee Trail,
                                       <br>Orlando, FL 32825</p>
            
                                </div>
                                <hr>
                                <div>
                                    
                            
                                    <span style="font-size: 15px; font-weight: 600;">Time:</span>
                                    <br>
                                    <p><?= htmlspecialchars($_SESSION["pickedTimeSlot"]) ?> / <?= htmlspecialchars($_SESSION["date"]) ?></p>
                                    <span style="font-size: 15px; font-weight: 600;">Sylist:</span>
                                    <br>
                                    <p><?= $_SESSION["stylist"] ?></p>
            
                                </div>
                                <hr>
                                <div class="promo">
                                    <span style="font-size: 15px; font-weight: 600;">Services:</span> 
                                    <div class="details">
                                        <?php foreach($_SESSION["services"] as $service): ?>
                                            <span><?= htmlspecialchars($service) ?></span><br>
                                        <?php endforeach; ?>
                                    </div>                           
                                </div>
                                <hr>
                                <div class="payments">
                                    <span style="font-size: 15px; font-weight: 600;">Deposit:</span>
                                    <div class="details">
                                        <span>$35.20</span>                                
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br>

                    <button type="submit" name="action" value="next" class="btn btn-outline-success my-2 my-sm-0" id="next" style="width: 12rem;">Book Appointment</button>

                    <br>

                    <button type="submit" name="action" value="back" class="btn btn-secondary" id="goback">Go Back</button>
                    

                </div>
            </form>
    <?php endif; ?>
    
    <script>
        const stylistSelect = document.getElementById("stylist");
        const stylistImage = document.getElementById("stylistImage");

        const images = {
            "Alondra": "Styles/stylist1.jpeg",
            "Henry": "Styles/stylist2.jpeg",
            "Christine": "Styles/stylist3.jpeg",
            "Sally": "Styles/stylist4.jpeg"
        };

        stylistSelect.addEventListener("change", function() {
            const selected = this.value;

            if (images[selected]) {
                stylistImage.src = images[selected];
                stylistImage.style.display = "block";
            } else {
                stylistImage.style.display = "none";
            }

        });

    </script>

</body>
</html>