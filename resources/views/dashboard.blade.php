<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>PPATH Dashboard</title>

<link rel="icon" href="/svg.png?v=3">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://cdn.tailwindcss.com"></script>

<script src="https://cdn.jsdelivr.net/npm/exceljs/dist/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>


</head>


<body class="bg-gray-100 pt-24">



<style>

body {
    margin:0;
    font-family:Arial,sans-serif;
    background:#f3f4f6;
    color:#1f2937;
}


main {
    max-width:1100px;
    margin:30px auto;
    padding:20px;
}


table {
    width:100%;
    border-collapse:collapse;
    background:white;
}
   .attendance-table {

    width: 100%;

    border-collapse: collapse;

    table-layout: auto;

}



.attendance-table th:nth-child(1),
.attendance-table td:nth-child(1){

    width: 70px;

}



.attendance-table th:nth-child(2),
.attendance-table td:nth-child(2){

    width: 300px;

    min-width:300px;

    max-width:500px;

    white-space: nowrap;

}



.attendance-table th:nth-child(3),
.attendance-table td:nth-child(3){

    width:120px;

}



.attendance-table td,
.attendance-table th {

    padding:12px;

    border:1px solid #e5e7eb;

    text-align:left;

}

th,td {

    padding:12px;
    border-bottom:1px solid #e5e7eb;
    text-align:left;

}


tbody tr:hover {

    background:#f3f4f6;

}


</style>





<!-- NAVBAR -->

<nav class="fixed top-0 left-0 w-full border-b shadow-sm px-8 py-4 flex justify-between items-center z-50"
style="background-color:#2F4B63;">



<div class="flex items-center gap-2">


<img src="/ppa1.png"
alt="PPATH Logo"
style="width:150px;height:auto;">



<span class="text-white text-lg ml-2">

| Monitoring System

</span>



</div>





<div class="flex items-center gap-6">


<div class="text-right">


<div class="text-xs uppercase text-white">

Logged In As

</div>



<div class="font-bold text-lg text-white">

Admin: {{ e(session('userName')) }}

</div>


</div>




<a href="/logout"

class="border border-red-500 text-red-500 px-5 py-2 rounded-lg hover:bg-red-50">

Log Out

</a>



</div>



</nav>







<!-- TITLE + BUTTONS -->


<div class="max-w-6xl mx-auto mt-6 px-4">


<div class="flex justify-between items-center">


<h2 class="text-2xl font-bold text-gray-900">

Registered Attendees Matrix

</h2>




<div class="flex gap-3">


<button onclick="addTable()"

class="text-white px-4 py-2 rounded-lg"

style="background-color:#6F8DA6;">

+ New Table

</button>



<a href="/mobile-uploads"

class="text-white px-4 py-2 rounded-lg"

style="background-color:#6F8DA6;">

Mobile Uploads

</a>



</div>


</div>


</div>







<main>


<div id="tablesContainer" class="space-y-8">



@forelse($activities as $activity)





<div class="bg-white border rounded-2xl shadow-sm p-8">





<!-- HEADER -->


<div class="flex justify-between items-center mb-4">


<div class="flex flex-col">


<h3 class="text-2xl font-bold text-gray-900">

{{ $activity->title }}

</h3>



<p class="text-sm text-gray-500 mt-1">

{{ \Carbon\Carbon::parse($activity->date)->format('F d, Y') }}

</p>



<p class="text-sm text-blue-600">

Created by: {{ $activity->author }}

</p>



</div>


</div>







<!-- EXPORT BUTTONS -->


<div class="flex justify-end gap-2 mb-4">


<button onclick="exportCSV(this)"

class="px-3 py-1 rounded border"

style="background-color:#D8DEE4;">

Export CSV

</button>




<button onclick="exportExcel(this)"

class="text-white px-3 py-1 rounded"

style="background-color:#0E1A24;">

Export Excel

</button>



</div>






<br>






<!-- STATISTICS DATA -->

@php


$total = $attendees
->where('activity_id',$activity->id)
->count();



$male = $attendees
->where('activity_id',$activity->id)
->where('gender','MALE')
->count();



$female = $attendees
->where('activity_id',$activity->id)
->where('gender','FEMALE')
->count();



@endphp






<!-- IMPORTANT FOR EXPORT -->

<div class="grid grid-cols-3 gap-4 mb-6 export-stats"

data-title="{{ $activity->title }}"

data-author="{{ $activity->author }}"

data-total="{{ $total }}"

data-male="{{ $male }}"

data-female="{{ $female }}">

    <div class="bg-blue-50 p-4 rounded-lg">
        <p class="text-gray-600">Total</p>
        <p id="total-{{ $activity->id }}" class="text-2xl font-bold text-blue-700">
    {{ $total }}
</p>
    </div>

    <div class="bg-green-50 p-4 rounded-lg">
        <p class="text-gray-600">Male</p>
        <p id="male-{{ $activity->id }}" class="text-2xl font-bold text-green-700">
    {{ $male }}
</p>
    </div>

    <div class="bg-purple-50 p-4 rounded-lg">
        <p class="text-gray-600">Female</p>
        <p id="female-{{ $activity->id }}" class="text-2xl font-bold text-purple-700">
    {{ $female }}
</p>
    </div>

</div>

<!-- ATTENDANCE TABLE -->

<!-- ATTENDANCE DROPDOWN -->

<div class="mt-4">
   <button
    onclick="toggleAttendance(this)"
    class="flex items-center gap-2 text-gray-700 hover:text-black transition">

    <span class="arrow transition-transform duration-300 inline-block">
    <svg class="w-3 h-3 fill-current text-black" viewBox="0 0 20 20">
        <path d="M7 5l6 5-6 5V5z"/>
    </svg>
</span>
    <span>Attendance List</span>

</button>
</div>

<div class="attendance-content hidden mt-4 overflow-x-auto">

    <table class="w-full border attendance-table">

        <thead class="bg-gray-100">
            <tr>
                <th class="border p-3">#</th>
                <th class="border p-3">Name</th>
                <th class="border p-3">Gender</th>
            </tr>
        </thead>

        <tbody id="attendance-{{ $activity->id }}">

            @php $count = 1; @endphp

            @forelse($attendees->where('activity_id',$activity->id) as $attendee)

            <tr>
                <td>{{ $count++ }}</td>
                <td class="border p-3 break-words">
                    {{ $attendee->name }}
                </td>
                <td>{{ $attendee->gender }}</td>
            </tr>

            @empty

            <tr>
                <td colspan="3" class="text-center text-gray-400">
                    No attendees yet
                </td>
            </tr>

            @endforelse

        </tbody>

    </table>

</div>



<br>





<!-- DELETE BUTTON -->

<a href="javascript:void(0)"

onclick="confirmDelete('/activities/delete/{{ $activity->id }}')"

class="text-white px-4 py-2 text-sm rounded hover:bg-red-600"

style="background-color:#CB0000;">

Delete table

</a>





</div>




@empty



<div id="emptyState"

class="text-center text-gray-400 text-sm mt-6">

No recent activity found yet.

</div>




@endforelse




</div>


</main>









<script>


function addTable(){


const container = document.getElementById("tablesContainer");


const emptyState =
document.getElementById("emptyState");



if(emptyState){

emptyState.remove();

}




const tableBlock =
document.createElement("div");



tableBlock.className =
"bg-white border rounded-2xl shadow-sm p-8";





tableBlock.innerHTML = `



<div class="mb-4">


<div class="flex gap-2">



<input

type="text"

placeholder="Enter Activity / Day Title"

class="activity-title flex-1 border p-2 rounded font-semibold">



<button

onclick="confirmTitle(this)"

class="bg-blue-600 text-white px-4 py-2 rounded">

Confirm

</button>



</div>





<input

type="date"

class="activity-date border p-2 rounded w-56 mt-2">





<input

type="text"

placeholder="Created By"

class="activity-author border p-2 rounded w-56 mt-2">



</div>









<div class="flex justify-end gap-2 mb-4">


<button onclick="exportCSV(this)"

class="px-3 py-1 rounded border"

style="background-color:#D8DEE4;">

Export CSV

</button>





<button onclick="exportExcel(this)"

class="text-white px-3 py-1 rounded"

style="background-color:#0E1A24;">

Export Excel

</button>



</div>







<div class="grid grid-cols-3 gap-4 mb-6 export-stats"

data-title=""

data-author=""

data-total="0"

data-male="0"

data-female="0">





<div class="bg-blue-50 p-4 rounded">

Total: 0

</div>



<div class="bg-green-50 p-4 rounded">

Male: 0

</div>



<div class="bg-purple-50 p-4 rounded">

Female: 0

</div>



</div>









<table class="w-full border attendance-table">


<thead class="bg-gray-100">

<tr>

<th>#</th>

<th>Name</th>

<th>Gender</th>

</tr>


</thead>



<tbody>


<tr>

<td colspan="3"

class="text-center text-gray-400 py-6">

No attendees yet

</td>


</tr>


</tbody>


</table>



`;





container.insertBefore(

tableBlock,

container.firstChild

);



}




function confirmDelete(url) {


Swal.fire({

title:'Delete Table?',

text:'This action cannot be undone.',

icon:'warning',

showCancelButton:true,

confirmButtonColor:'#CB0000',

cancelButtonText:'Cancel',

confirmButtonText:'Delete'

}).then((result)=>{


if(result.isConfirmed){

window.location.href=url;

}


});


}







function confirmTitle(button){


const tableBlock =
button.closest('.bg-white');



const title =
tableBlock.querySelector('.activity-title').value.trim();



const date =
tableBlock.querySelector('.activity-date').value;



const author =
tableBlock.querySelector('.activity-author').value.trim();





if(!title || !date || !author){


Swal.fire({

icon:'warning',

text:'Please enter Title, Date and Author.',

timer:1500,

showConfirmButton:false

});


return;


}






fetch('/activities',{

method:'POST',

headers:{

'Content-Type':'application/json',

'X-CSRF-TOKEN':'{{ csrf_token() }}'

},


body:JSON.stringify({

title:title,

date:date,

author:author

})


})



.then(res=>res.json())


.then(data=>{


if(data.success){


Swal.fire({

icon:'success',

title:'Saved!',

text:'Activity created successfully.',

timer:1500,

showConfirmButton:false

}).then(()=>{


window.location.reload();


});



}


});



}









// ================================
// EXPORT EXCEL
// ================================


// ================================
// EXPORT EXCEL
// ================================

async function exportExcel(button){

    const card = button.closest('.bg-white');
    const table = card.querySelector('table');
    const stats = card.querySelector('.export-stats');

    const title = stats.dataset.title || "PPATH Attendance";
    const author = stats.dataset.author || "";
    const total = stats.dataset.total || 0;
    const male = stats.dataset.male || 0;
    const female = stats.dataset.female || 0;

    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet("Attendance");

    // ==========================
    // REPORT HEADER
    // ==========================

    worksheet.mergeCells("A1:C1");
    worksheet.getCell("A1").value = "PPATH Attendance Report";
   worksheet.getCell("A1").font = {
    bold: true,
    size: 18,
    color: { argb: "FFFFFFFF" }
};

worksheet.getCell("A1").alignment = {
    horizontal: "center",
    vertical: "middle"
};

worksheet.getCell("A1").fill = {
    type: "pattern",
    pattern: "solid",
    fgColor: { argb: "2F4B63" }
};

worksheet.getRow(1).height = 28;

let row = worksheet.addRow([
    "Activity",
    title,
    ""
]);

row.getCell(1).font = {
    bold:true
};

worksheet.mergeCells(`B${row.number}:C${row.number}`);



row = worksheet.addRow([
    "Created By",
    author,
    ""
]);

row.getCell(1).font = {
    bold:true
};

worksheet.mergeCells(`B${row.number}:C${row.number}`);


   let summary = worksheet.addRow([
    "Attendance Summary",
    "",
    ""
]);

worksheet.mergeCells(
    `A${summary.number}:C${summary.number}`
);

summary.font = {
    bold: true,
    color: { argb: "FFFFFFFF" }
};

summary.getCell(1).fill = {
    type: "pattern",
    pattern: "solid",
    fgColor: { argb: "6F8DA6" }
};

 let totalRow = worksheet.addRow([
    "Total",
    total,
    ""
]);

worksheet.mergeCells(`B${totalRow.number}:C${totalRow.number}`);



let maleRow = worksheet.addRow([
    "Male",
    male,
    ""
]);

worksheet.mergeCells(`B${maleRow.number}:C${maleRow.number}`);



let femaleRow = worksheet.addRow([
    "Female",
    female,
    ""
]);

worksheet.mergeCells(`B${femaleRow.number}:C${femaleRow.number}`);
 
    

    // ==========================
    // ATTENDANCE TABLE
    // ==========================

    const rows = table.querySelectorAll("tr");

    rows.forEach(row => {

        let rowData = [];

        row.querySelectorAll("th, td").forEach(cell => {
            rowData.push(cell.innerText.trim());
        });

        worksheet.addRow(rowData);

    });

    // ==========================
    // STYLE HEADER ROW
    // ==========================

    const headerRowNumber = 8;

   const headerRow = worksheet.getRow(headerRowNumber);

headerRow.font = {
    bold: true,
    color: { argb: "FFFFFFFF" }
};

headerRow.alignment = {
    horizontal: "center",
    vertical: "middle"
};

headerRow.eachCell(cell => {

    cell.fill = {
        type: "pattern",
        pattern: "solid",
        fgColor: { argb: "2F4B63" }
    };

});

    // ==========================
    // BORDERS & ALIGNMENT
    // ==========================

  worksheet.eachRow((row) => {

    row.eachCell({ includeEmpty: true }, (cell) => {

        cell.border = {

            top: {
                style: "thin"
            },

            left: {
                style: "thin"
            },

            bottom: {
                style: "thin"
            },

            right: {
                style: "thin"
            }

        };


        cell.alignment = {

            horizontal: "center",

            vertical: "middle",

            wrapText: true

        };


    });

});
   

    // ==========================
    // AUTO SIZE COLUMNS
    // ==========================

    worksheet.columns.forEach(column => {

        let maxLength = 10;

        column.eachCell({ includeEmpty: true }, cell => {

            const length = cell.value
                ? cell.value.toString().length
                : 0;

            if (length > maxLength) {
                maxLength = length;
            }

        });

        column.width = maxLength + 3;

    });

    // ==========================
    // DOWNLOAD
    // ==========================

    const buffer = await workbook.xlsx.writeBuffer();
    worksheet.pageSetup = {

    orientation: "landscape",

    fitToPage: true,

    fitToWidth: 1,

    fitToHeight: 0

};
worksheet.views = [
    {
        state: "frozen",
        ySplit: headerRowNumber
    }
];
    saveAs(
        new Blob(
            [buffer],
            {
                type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
            }
        ),
        title + "_Attendance_Report.xlsx"
    );

}









// ================================
// EXPORT CSV
// ================================


function exportCSV(button){



const card =
button.closest('.bg-white');



const table =
card.querySelector('table');



const stats =
card.querySelector('.export-stats');





const title =
stats.dataset.title || "PPATH Attendance";



const author =
stats.dataset.author || "";



const total =
stats.dataset.total || 0;



const male =
stats.dataset.male || 0;



const female =
stats.dataset.female || 0;






let csv=[];



csv.push(

`"PPATH Attendance Report"`

);



csv.push(

`"Activity","${title}"`

);



csv.push(

`"Created By","${author}"`

);



csv.push("");





csv.push(

`"Attendance Summary"`

);



csv.push(

`"Total","${total}"`

);



csv.push(

`"Male","${male}"`

);



csv.push(

`"Female","${female}"`

);



csv.push("");







table.querySelectorAll("tr")

.forEach(row=>{


let rowData=[];



row.querySelectorAll("th,td")

.forEach(cell=>{


rowData.push(

`"${cell.innerText.trim()}"`

);



});



csv.push(

rowData.join(",")

);



});







const blob = new Blob(

[csv.join("\n")],

{

type:"text/csv"

}

);





const url =
URL.createObjectURL(blob);




const a =
document.createElement("a");



a.href=url;



a.download =
title+"_Attendance_Report.csv";




document.body.appendChild(a);



a.click();



document.body.removeChild(a);



URL.revokeObjectURL(url);



}








// ================================
// LIVE ATTENDANCE UPDATE
// ================================


async function loadAttendance(activityId){



try{


const response =
await fetch(`/attendance/live/${activityId}`);



const data =
await response.json();





const tbody =
document.getElementById(`attendance-${activityId}`);




if(tbody){



tbody.innerHTML="";



if(data.attendees.length>0){



let count=1;



data.attendees.forEach(att=>{


tbody.innerHTML += `

<tr>

<td class="border p-3">
${count++}
</td>


<td class="border p-3 whitespace-nowrap">
${att.name}
</td>


<td class="border p-3">
${att.gender}
</td>


</tr>

`;



});



}

else{


tbody.innerHTML=`

<tr>

<td colspan="3"

class="text-center text-gray-400 py-6">

No attendees yet

</td>

</tr>

`;



}



}







const totalBox =
document.getElementById(`total-${activityId}`);



const maleBox =
document.getElementById(`male-${activityId}`);



const femaleBox =
document.getElementById(`female-${activityId}`);





if (totalBox) {
    totalBox.textContent = data.total;
}

if (maleBox) {
    maleBox.textContent = data.male;
}

if (femaleBox) {
    femaleBox.textContent = data.female;
}





}

catch(error){

console.error(error);

}


}







function initLiveUpdate(){


@foreach($activities as $activity)

loadAttendance({{ $activity->id }});

@endforeach


}




window.addEventListener(

"load",

function(){

initLiveUpdate();

}

);




setInterval(

initLiveUpdate,

2000

);




</script>
<script>
   function toggleAttendance(button)
{
    const content = button.parentElement.nextElementSibling;
    const arrow = button.querySelector(".arrow");

    content.classList.toggle("hidden");

    if (content.classList.contains("hidden"))
    {
        arrow.style.transform = "rotate(0deg)";
    }
    else
    {
        arrow.style.transform = "rotate(90deg)";
    }
}
</script>
 <script>
        // ADD COMPLETE BORDERS

worksheet.eachRow({ includeEmpty: true }, function(row){

    row.eachCell({ includeEmpty: true }, function(cell){

        cell.border = {

            top:{
                style:"thin"
            },

            left:{
                style:"thin"
            },

            bottom:{
                style:"thin"
            },

            right:{
                style:"thin"
            }

        };

    });

});
     // FORCE TABLE AREA BORDER

for(let row = 10; row <= worksheet.rowCount; row++){

    for(let col = 1; col <= 3; col++){

        worksheet.getCell(row,col).border = {

            top:{style:"thin"},
            left:{style:"thin"},
            bottom:{style:"thin"},
            right:{style:"thin"}

        };

    }

}
        </script>

</body>

</html>
