<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPATH QR Scanner</title>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://unpkg.com/html5-qrcode"></script>

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:"Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        body{
            background:#f4f6f9;
            min-height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            padding:30px;
        }

        .container{
            width:100%;
            max-width:720px;
            background:#fff;
            border-radius:12px;
            box-shadow:0 10px 30px rgba(0,0,0,.08);
            overflow:hidden;
        }

        .header{
            background:#1f4e79;
            color:#fff;
            padding:25px 30px;
        }

        .header h1{
            font-size:28px;
            font-weight:600;
            margin-bottom:5px;
        }

        .header p{
            font-size:14px;
            opacity:.9;
        }

        .content{
            padding:30px;
        }

        .form-group{
            margin-bottom:25px;
        }

        label{
            display:block;
            margin-bottom:8px;
            font-weight:600;
            color:#333;
        }

        select{
            width:100%;
            padding:12px;
            border:1px solid #ced4da;
            border-radius:8px;
            font-size:15px;
            outline:none;
            background:#fff;
        }

        select:focus{
            border-color:#1f4e79;
            box-shadow:0 0 0 3px rgba(31,78,121,.15);
        }

   #reader{
    width: 100%;
    max-width: 500px;
    margin: 0 auto;
    border: 1px solid #dee2e6;
    border-radius: 10px;
    background: #fff;
    overflow: hidden;
}
#reader video,
#reader canvas{
    width: 100% !important;
    height: auto !important;
    display: block;
}
        
        #reader__filescan_region,
        #reader__dashboard_section_swaplink{
            display:none !important;
        }

        .btn-group{
            margin-top:15px;
            text-align:center;
        }

        button{
            background:#1f4e79;
            color:#fff;
            border:none;
            padding:12px 18px;
            margin:5px;
            border-radius:8px;
            font-size:15px;
            font-weight:600;
            cursor:pointer;
        }

        button:hover{
            background:#163a5c;
        }

        .footer{
            text-align:center;
            padding:18px;
            border-top:1px solid #e9ecef;
            color:#777;
            font-size:13px;
            background:#fafafa;
        }

        @media(max-width:768px){
            body{ padding:15px; }
            .header{ padding:20px; }
            .content{ padding:20px; }
            .header h1{ font-size:24px; }
        }
    </style>
</head>

<body>

<div class="container">

    <div class="header" style="display:flex;justify-content:space-between;align-items:center;">

    <div>
        <h1>PPATH QR Scanner</h1>
        <p>Scan participant QR codes to record attendance.</p>
    </div>

    <button onclick="logoutScanner()" style="
        background:#FF2400;
        padding:10px 18px;
        border:none;
        border-radius:8px;
        color:white;
        font-weight:600;
        cursor:pointer;
    ">
        Exit Scanner
    </button>

</div>

    <div class="content">

        <div class="form-group">
            <label>Select Attendance Activity</label>

            <select id="activity_id">
                @foreach($activities as $activity)
                    <option value="{{ $activity->id }}">
                        {{ $activity->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <div id="reader"></div>

        <div class="btn-group">
            <button id="startBtn">Start Scanning</button>
            <button id="stopBtn" style="display:none;">Stop Scanning</button>
        </div>

    </div>

    <div class="footer">
        PPATH Attendance Monitoring System
    </div>

</div>

<script>

let html5QrCode = new Html5Qrcode("reader");
let isScanning = false;

document.getElementById("startBtn").addEventListener("click", async () => {

    try {
        const cameras = await Html5Qrcode.getCameras();

        if (!cameras.length) {
            alert("No camera found");
            return;
        }

      await html5QrCode.start(
    { facingMode: "environment" },
    {
        fps: 10,
        qrbox: 250
    },
    onScanSuccess
);

        document.getElementById("startBtn").style.display = "none";
        document.getElementById("stopBtn").style.display = "inline-block";

    } catch (err) {
        console.error(err);
        alert("Camera error: " + err);
    }

});

document.getElementById("stopBtn").addEventListener("click", async () => {

    await html5QrCode.stop();
    await html5QrCode.clear();

    document.getElementById("startBtn").style.display = "inline-block";
    document.getElementById("stopBtn").style.display = "none";

});

function onScanSuccess(decodedText) {

    if (isScanning) return;
    isScanning = true;

    let activity_id = document.getElementById('activity_id').value;

    let data = decodedText.split("/");

    if (data.length !== 2) {
        alert("Invalid QR Format!\nExpected: Name/Gender");
        resetScanner();
        return;
    }

    let name = data[0].trim();
    let gender = data[1].trim();

    fetch('/scanner/save', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            activity_id: activity_id,
            name: name,
            gender: gender
        })
    })
    .then(res => res.json())
    .then(data => {

        if (data.success) {
            alert("Attendance Recorded Successfully");
        } else {
            alert(data.message || "Failed");
        }

    })
    .catch(err => {
        console.error(err);
        alert("Error occurred");
    })
    .finally(() => {
        resetScanner();
    });
}

function resetScanner() {
    setTimeout(() => {
        isScanning = false;
    }, 2000);
}

</script>
<script>
    function logoutScanner() {

    Swal.fire({
        title: 'Exit Scanner?',
        text: 'You will need to enter the scanner PIN again to continue.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1f4e79',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Exit',
        cancelButtonText: 'Cancel'
    }).then((result) => {

        if (result.isConfirmed) {
            window.location.href = "/scanner/logout";
        }

    });

}
</script>
</body>
</html>
