<?php
// Start session and check login
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>New Record</title>
  <link rel="stylesheet" href="css/style.css" />
  <style>
    .hidden { display: none; }
    .suggestions { position: relative; }
    .listbox {
      position: absolute; top: 38px; left: 0; right: 0;
      background: white; border: 1px solid #ccc;
      max-height: 200px; overflow-y: auto; z-index: 100;
    }
    .listbox div { padding: 6px 10px; cursor: pointer; }
    .listbox div:hover { background: #eee; }

    .interview-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    .interview-table th, .interview-table td { border: 1px solid #ccc; padding: 8px; text-align: center; }
    .interview-table th { background-color: #f2f2f2; font-weight: bold; }
    .interview-table input, .interview-table select { width: 95%; padding: 4px; font-size: 14px; }

    .button-group { margin-top: 15px; display: flex; justify-content: flex-end; gap: 10px; }

    .topbar-right { display: flex; justify-content: flex-end; align-items: center; gap: 10px; margin-bottom: 15px; }
    .user-name { font-weight: bold; }
    .btn.subtle { padding: 5px 10px; cursor: pointer; }
  </style>
</head>
<body>
  <div class="container">
    <div class="topbar-right">
      <span id="userName" class="user-name">Welcome, <?php echo htmlspecialchars($_SESSION['email']); ?></span>
      <form method="POST" style="display:inline;">
        <button name="logout" class="btn subtle">Logout</button>
      </form>
    </div>

    <header>
      <h1>Create New Facility Record</h1>
      <div class="btn-group">
        <a href="index.php" class="btn">Home</a>
        <a href="search.php" class="btn">Search</a>
        <a href="update.php" class="btn">Update</a>
        <a href="delete.php" class="btn">Delete</a>
      </div>
    </header>

    <section class="card">
      <form id="newForm">
        <!-- PAGE 1 -->
        <div id="page1">
          <h2>A. Identification</h2>
          <div class="form-grid">
            <div class="input-group">
              <label for="newFirst">First Name</label>
              <input type="text" id="newFirst" required />
            </div>
            <div class="input-group">
              <label for="newMiddle">Middle Name</label>
              <input type="text" id="newMiddle" />
            </div>
            <div class="input-group">
              <label for="newLast">Surname</label>
              <input type="text" id="newLast" required />
            </div>
            <div class="input-group">
              <label for="newSuffix">Suffix</label>
              <input type="text" id="newSuffix" placeholder="e.g., Jr, Sr" />
            </div>
            <div class="input-group">
              <label for="newProvince">Province</label>
              <div class="suggestions">
                <input type="text" id="newProvince" autocomplete="off" required />
                <div id="provinceList" class="listbox hidden"></div>
              </div>
            </div>
            <div class="input-group">
              <label for="newCity">City/Municipality</label>
              <div class="suggestions">
                <input type="text" id="newCity" autocomplete="off" required />
                <div id="cityList" class="listbox hidden"></div>
              </div>
            </div>
            <div class="input-group">
              <label for="newBarangay">Barangay</label>
              <input type="text" id="newBarangay" required />
            </div>
            <div class="input-group">
              <label for="newAddress">Address</label>
              <input type="text" id="newAddress" required />
            </div>
            <div class="input-group">
              <label for="newHouseholdHead">Household Head</label>
              <input type="text" id="newHouseholdHead" required />
            </div>
            <div class="input-group">
              <label for="newNumOfHouseholdMem">Total Number of Household Members</label>
              <input type="number" id="newNumOfHouseholdMem" required min="1" />
            </div>
          </div>
          <div class="button-group">
            <button type="reset" class="btn secondary">Clear</button>
            <button type="button" id="nextBtn1" class="btn">Next</button>
          </div>
        </div>

        <!-- PAGE 2 -->
        <div id="page2" class="hidden">
          <h2>B. Interview Information</h2>
          <table class="interview-table">
            <thead>
              <tr>
                <th>Visit</th>
                <th>Date of Visit</th>
                <th>Time Start</th>
                <th>Time End</th>
                <th>Result<br />(C=Completed, CB=Callback, R=Refused)</th>
                <th>Date of Next Visit</th>
                <th>Name of Interviewer, Initial/Date</th>
                <th>Name of Supervisor, Initial/Date</th>
              </tr>
            </thead>
            <tbody>
              <?php for($i=1;$i<=2;$i++): ?>
              <tr>
                <td><?php echo $i; ?><?php echo $i==1?'st':'nd'; ?> Visit</td>
                <td><input type="date" id="visit<?php echo $i; ?>Date" /></td>
                <td><input type="time" id="visit<?php echo $i; ?>Start" /></td>
                <td><input type="time" id="visit<?php echo $i; ?>End" /></td>
                <td>
                  <select id="visit<?php echo $i; ?>Result">
                    <option value="">Select</option>
                    <option value="C">C - Completed</option>
                    <option value="CB">CB - Callback</option>
                    <option value="R">R - Refused</option>
                  </select>
                </td>
                <td><input type="date" id="visit<?php echo $i; ?>NextDate" /></td>
                <td><input type="text" id="visit<?php echo $i; ?>Interviewer" placeholder="Initial/Date" /></td>
                <td><input type="text" id="visit<?php echo $i; ?>Supervisor" placeholder="Initial/Date" /></td>
              </tr>
              <?php endfor; ?>
            </tbody>
          </table>
          <div class="button-group">
            <button type="reset" class="btn secondary">Clear</button>
            <button type="button" id="backBtn2" class="btn secondary">Back</button>
            <button type="button" id="nextBtn2" class="btn">Next</button>
          </div>
        </div>

        <!-- PAGE 3 -->
        <div id="page3" class="hidden">
          <h2>C. Encoding Information</h2>
          <div class="button-group">
            <button type="reset" class="btn secondary">Clear</button>
            <button type="button" id="backBtn3" class="btn secondary">Back</button>
            <button type="submit" id="submitBtn" class="btn">Submit</button>
          </div>
        </div>
      </form>
    </section>
  </div>

  <script>
    // Logout handling via POST
    document.querySelector('form[method="POST"]').addEventListener('submit', function(e){
      if(e.submitter && e.submitter.name === 'logout'){
        e.preventDefault();
        fetch(window.location.href, {method:'POST', body: new URLSearchParams({logout:'1'})})
          .then(()=> window.location.href='login.php');
      }
    });

    <?php
    // Handle logout
    if(isset($_POST['logout'])){
        session_destroy();
        header("Location: login.php");
        exit;
    }
    ?>

    // Province/City dropdowns
    const provinces = ["Benguet","Metro Manila","Cebu","Batangas","Cavite","Laguna","Pampanga","Bulacan","Davao del Sur"];
    const cities = {
      "Metro Manila":["Manila","Quezon City","Pasig","Makati","Taguig","Caloocan"],
      "Benguet":["Baguio City","La Trinidad"],
      "Cebu":["Cebu City","Mandaue","Lapu-Lapu"],
      "Batangas":["Batangas City","Lipa","Tanauan"],
      "Cavite":["Dasmariñas","Imus","Bacoor"],
      "Laguna":["Calamba","Santa Rosa","Biñan"],
      "Pampanga":["San Fernando","Angeles"],
      "Bulacan":["Malolos","San Jose del Monte"],
      "Davao del Sur":["Davao City","Digos"]
    };

    function setupDropdown(inputId, listId, data){
      const input = document.getElementById(inputId);
      const list = document.getElementById(listId);

      input.addEventListener("input", () => {
        const value = input.value.toLowerCase();
        list.innerHTML = "";
        const matches = data.filter(d=>d.toLowerCase().includes(value));
        matches.forEach(item=>{
          const div=document.createElement("div");
          div.textContent=item;
          div.onclick=()=> {input.value=item; list.classList.add("hidden");};
          list.appendChild(div);
        });
        list.classList.toggle("hidden", matches.length===0);
      });

      document.addEventListener("click", e=>{
        if(!input.contains(e.target) && !list.contains(e.target)){
          list.classList.add("hidden");
        }
      });
    }

    setupDropdown("newProvince","provinceList",provinces);

    document.getElementById("newProvince").addEventListener("change", ()=>{
      const selected = document.getElementById("newProvince").value;
      const cityInput = document.getElementById("newCity");
      cityInput.value = "";
      const cityList = document.getElementById("cityList");
      cityList.innerHTML = "";
      setupDropdown("newCity","cityList", cities[selected] || []);
    });

    // Page navigation
    const page1=document.getElementById("page1");
    const page2=document.getElementById("page2");
    const page3=document.getElementById("page3");

    document.getElementById("nextBtn1").addEventListener("click", ()=>{ page1.classList.add("hidden"); page2.classList.remove("hidden"); });
    document.getElementById("backBtn2").addEventListener("click", ()=>{ page2.classList.add("hidden"); page1.classList.remove("hidden"); });
    document.getElementById("nextBtn2").addEventListener("click", ()=>{ page2.classList.add("hidden"); page3.classList.remove("hidden"); });
    document.getElementById("backBtn3").addEventListener("click", ()=>{ page3.classList.add("hidden"); page2.classList.remove("hidden"); });
  </script>
</body>
</html>
