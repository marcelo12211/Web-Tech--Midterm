<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$logged_in_username = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Staff';

$resident_name = "________________________";
$address = "________________________";
$years = 15;
$date = date("F d, Y");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Certificate of Residency - Staff</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
    <link rel="stylesheet" href="css/style.css">
    <style>
        @media print {
            body * { visibility: hidden; }
            .certificate-container, .certificate-container * { visibility: visible; }
            .certificate-container { 
                position: absolute; 
                left: 0; 
                top: 0; 
                width: 100%;
                border: none;
                box-shadow: none;
            }
            .no-print { display: none; }
        }
        
        body {
            margin: 0;
            font-family: "Roboto", Arial, sans-serif;
            background: #f8f9fa;
        }
        
        .cert-card {
            max-width: 800px;
            margin: 30px auto;
            padding: 50px;
            border: 2px solid #ccc;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            background: white;
        }
        .cert-header { text-align: center; margin-bottom: 40px; }
        .cert-header h1 { font-size: 2rem; color: #007bff; margin-bottom: 5px; }
        .cert-body { font-size: 1.1rem; line-height: 1.8; }
        .cert-body p { margin-bottom: 20px; }
        .cert-body .recipient { font-weight: bold; text-decoration: underline; }
        .cert-body .indent { margin-left: 50px; text-indent: -50px; }
        .cert-footer { margin-top: 50px; text-align: right; }
        .cert-footer .signee { font-weight: bold; border-top: 1px solid #000; display: inline-block; padding-top: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; padding: 20px;">
        <a href="staff_documents.php" class="btn"><i class="fas fa-arrow-left"></i> Back to Documents</a>
        <button onclick="window.print()" class="btn primary-btn"><i class="fas fa-print"></i> Print Certificate</button>
    </div>
    
    <div class="certificate-container cert-card">
        <div class="cert-header">
            <p style="font-size: 1rem; color: #5f6368; margin-bottom: 0;">Republic of the Philippines</p>
            <p style="font-size: 1.2rem; color: #202124; margin: 0;">Barangay Happy Hallow, Marilao, Bulacan</p>
            <h1 style="color: #007bff;">CERTIFICATE OF RESIDENCY</h1>
            <p style="margin-top: 20px; font-weight: 500;">TO WHOM IT MAY CONCERN:</p>
        </div>
        
        <div class="cert-body">
            <p class="indent">This is to certify that <span class="recipient"><?php echo htmlspecialchars($resident_name); ?></span>, of legal age, is a legitimate resident of <span class="recipient"><?php echo htmlspecialchars($address); ?></span>, in this Barangay.</p>

            <p class="indent">The aforementioned person has continuously resided in this Barangay for **<?php echo htmlspecialchars($years); ?>** year(s) and is known to us as a law-abiding citizen.</p>
            
            <p class="indent">This certification is issued for whatever legal purpose it may serve the party concerned.</p>
            
            <p class="indent" style="margin-top: 40px;">Given this <span class="recipient"><?php echo $date; ?></span>, at the Office of the Punong Barangay, Happy Hallow, Marilao, Bulacan.</p>
        </div>
        
        <div class="cert-footer">
            <p style="margin: 0; font-size: 1rem;">Prepared by:</p>
            <p class="signee">
                HON. MERRY JANE SMITH
                <br>
                Punong Barangay
            </p>
        </div>
    </div>

<script>
function setupLogout() {
    const logoutBtn = document.getElementById("logoutBtn");
    if (logoutBtn) {
        logoutBtn.addEventListener("click", () => {
            window.location.href = "logout.php";
        });
    }
}

window.onload = function () {
    setupLogout();
};
</script>
</body>
</html>