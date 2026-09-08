<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Mobile Uploads</title>
    <script src="https://cdn.tailwindcss.com"></script>
 <script src="https://cdn.jsdelivr.net/npm/exceljs/dist/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" href="/svg.png?v=3">
</head>

<body class="bg-gray-100 pt-24">

<nav class="fixed top-0 left-0 w-full border-b shadow-sm px-8 py-4 flex justify-between items-center z-50"
     style="background-color:#2F4B63;">
    <div class="flex items-center gap-2">
        <img src="/ppa1.png" alt="PPATH Logo" style="width:150px;height:auto;">
        <span class="text-white text-lg ml-2">| Monitoring System</span>
    </div>
    <a href="/dashboard" class="bg-white text-gray-700 px-5 py-2 rounded-lg hover:bg-gray-200">
        ← Back to Dashboard
    </a>
</nav>

<main class="max-w-6xl mx-auto mt-6 px-4">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Uploads</h1>
        <p class="text-gray-500 mt-1">Attendance records uploaded from mobile</p>
    </div>

    @if($uploads->count() == 0)
    <div class="bg-white rounded-2xl shadow-sm p-8 text-center text-gray-400">
        No uploaded attendance yet.
    </div>
    @else
    <div class="space-y-8">
        @foreach($uploads as $upload)
        @php
            $total = $upload->attendees->count();
            $male = $upload->attendees->where('gender','MALE')->count();
            $female = $upload->attendees->where('gender','FEMALE')->count();
        @endphp

        <div class="bg-white border rounded-2xl shadow-sm p-8 upload-card"
             data-title="{{ $upload->table_name }}"
             data-author="{{ $upload->author }}"
             data-total="{{ $total }}"
             data-male="{{ $male }}"
             data-female="{{ $female }}">

            <div class="flex justify-between items-center mb-5">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $upload->table_name }}</h2>
                    <p class="text-sm text-gray-500 mt-1">Uploaded by: {{ $upload->author }}</p>
                    <p class="text-sm text-blue-600">Mobile Attendance Upload</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="exportCSV(this)" class="px-3 py-1 rounded border" style="background-color:#D8DEE4;">Export CSV</button>
                    <button onclick="exportExcel(this)" class="text-white px-3 py-1 rounded" style="background-color:#0E1A24;">Export Excel</button>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-gray-600">Total</p>
                    <p class="text-2xl font-bold text-blue-700">{{ $total }}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <p class="text-gray-600">Male</p>
                    <p class="text-2xl font-bold text-green-700">{{ $male }}</p>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg">
                    <p class="text-gray-600">Female</p>
                    <p class="text-2xl font-bold text-purple-700">{{ $female }}</p>
                </div>
            </div>

        <!-- Dropdown Button -->
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

<!-- Hidden Attendance Table -->
<div class="attendance-content hidden mt-4 overflow-x-auto">

    <table class="w-full border attendance-table">
        <thead class="bg-gray-100">
            <tr>
                <th class="border p-3">#</th>
                <th class="border p-3">Name</th>
                <th class="border p-3">Gender</th>
            </tr>
        </thead>

        <tbody>
            @php $count = 1; @endphp

            @forelse($upload->attendees as $person)
            <tr>
                <td class="border p-3">{{ $count++ }}</td>
                <td class="border p-3">{{ $person->name }}</td>
                <td class="border p-3">{{ $person->gender }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center text-gray-400 p-6">
                    No attendees yet
                </td>
            </tr>
            @endforelse

        </tbody>
    </table>

</div>
              <br>

            <a href="javascript:void(0)"
               onclick="confirmDelete('/mobile-uploads/delete/{{ $upload->id }}')"
               class="text-white px-4 py-2 text-sm rounded hover:bg-red-600"
               style="background-color:#CB0000;">

                Delete table

            </a>
        </div>
        @endforeach
    </div>
    @endif
</main>

<script>
async function exportExcel(button)
{
    const card = button.closest('.upload-card');
    const table = card.querySelector('table');

    const title = card.dataset.title || "PPATH Mobile Attendance";
    const author = card.dataset.author || "";
    const total = card.dataset.total || 0;
    const male = card.dataset.male || 0;
    const female = card.dataset.female || 0;


    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet("Attendance");


    // ==========================
    // REPORT HEADER
    // ==========================

    worksheet.mergeCells("A1:C1");

    worksheet.getCell("A1").value =
        "PPATH Mobile Attendance Report";


    worksheet.getCell("A1").font = {
        bold:true,
        size:18,
        color:{argb:"FFFFFFFF"}
    };


    worksheet.getCell("A1").alignment = {
        horizontal:"center",
        vertical:"middle"
    };


    worksheet.getCell("A1").fill = {
        type:"pattern",
        pattern:"solid",
        fgColor:{argb:"2F4B63"}
    };


    worksheet.getRow(1).height = 28;



    // ACTIVITY

    let row = worksheet.addRow([
        "Activity",
        title,
        ""
    ]);

    row.getCell(1).font = {
        bold:true
    };

    worksheet.mergeCells(
        `B${row.number}:C${row.number}`
    );



    // AUTHOR

    row = worksheet.addRow([
        "Uploaded By",
        author,
        ""
    ]);


    row.getCell(1).font = {
        bold:true
    };


    worksheet.mergeCells(
        `B${row.number}:C${row.number}`
    );




    // SUMMARY HEADER

    let summary = worksheet.addRow([
        "Attendance Summary",
        "",
        ""
    ]);


    worksheet.mergeCells(
        `A${summary.number}:C${summary.number}`
    );


    summary.font = {
        bold:true,
        color:{argb:"FFFFFFFF"}
    };


    summary.getCell(1).fill = {
        type:"pattern",
        pattern:"solid",
        fgColor:{argb:"6F8DA6"}
    };




    // TOTAL

    let totalRow = worksheet.addRow([
        "Total",
        total,
        ""
    ]);

    worksheet.mergeCells(
        `B${totalRow.number}:C${totalRow.number}`
    );




    // MALE

    let maleRow = worksheet.addRow([
        "Male",
        male,
        ""
    ]);


    worksheet.mergeCells(
        `B${maleRow.number}:C${maleRow.number}`
    );





    // FEMALE

    let femaleRow = worksheet.addRow([
        "Female",
        female,
        ""
    ]);


    worksheet.mergeCells(
        `B${femaleRow.number}:C${femaleRow.number}`
    );




    // ==========================
    // ATTENDANCE TABLE
    // ==========================


    const rows = table.querySelectorAll("tr");


 const attendanceStartRow = worksheet.rowCount + 1;


rows.forEach(row=>{

        let rowData=[];


        row.querySelectorAll("th,td")
        .forEach(cell=>{

            rowData.push(
                cell.innerText.trim()
            );

        });


        worksheet.addRow(rowData);

    });




    // Attendance header position

 const headerRowNumber = attendanceStartRow;


    const headerRow =
        worksheet.getRow(headerRowNumber);



    headerRow.font = {

        bold:true,

        color:{argb:"FFFFFFFF"}

    };


    headerRow.alignment = {

        horizontal:"center",

        vertical:"middle"

    };



    headerRow.eachCell(cell=>{

        cell.fill = {

            type:"pattern",

            pattern:"solid",

            fgColor:{argb:"2F4B63"}

        };

    });






    // ==========================
    // BORDERS
    // ==========================


    worksheet.eachRow(row=>{


        row.eachCell(
            {includeEmpty:true},
            cell=>{


               cell.border = {

    top:{
        style:"thin",
        color:{argb:"FF000000"}
    },

    left:{
        style:"thin",
        color:{argb:"FF000000"}
    },

    bottom:{
        style:"thin",
        color:{argb:"FF000000"}
    },

    right:{
        style:"thin",
        color:{argb:"FF000000"}
    }

};


               cell.alignment = {

    horizontal:"center",

    vertical:"middle",

    wrapText:true

};


            }
        );


    });







    // ==========================
    // AUTO WIDTH
    // ==========================


    worksheet.columns.forEach(column=>{


        let maxLength = 10;


        column.eachCell(
            {includeEmpty:true},
            cell=>{


                let length =
                cell.value
                ? cell.value.toString().length
                : 0;



                if(length > maxLength)
                {
                    maxLength = length;
                }


            }
        );


        column.width =
        maxLength + 3;


    });



worksheet.views = [
    {
        state:"frozen",
        ySplit:headerRowNumber
    }
];

    // ==========================
    // DOWNLOAD
    // ==========================


    const buffer =
        await workbook.xlsx.writeBuffer();


    saveAs(

        new Blob(
            [buffer],
            {
                type:
                "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
            }
        ),

        title+"_Mobile_Attendance.xlsx"

    );

}

function exportCSV(button)
{
    const card = button.closest('.upload-card');
    const table = card.querySelector('table');

    const title = card.dataset.title;
    const author = card.dataset.author;
    const total = card.dataset.total;
    const male = card.dataset.male;
    const female = card.dataset.female;

    let csv = [];

    // REPORT HEADER
    csv.push(`"PPATH Mobile Attendance Report"`);
    csv.push(`"Activity","${title}"`);
    csv.push(`"Uploaded By","${author}"`);
    csv.push("");

    // SUMMARY
    csv.push(`"Attendance Summary"`);
    csv.push(`"Total","${total}"`);
    csv.push(`"Male","${male}"`);
    csv.push(`"Female","${female}"`);
    csv.push("");

    // TABLE DATA
    const rows = table.querySelectorAll("tr");
    rows.forEach(row => {
        let rowData = [];
        row.querySelectorAll("th, td").forEach(cell => {
            rowData.push(`"${cell.innerText.trim()}"`);
        });
        csv.push(rowData.join(","));
    });

    const blob = new Blob([csv.join("\n")], { type:"text/csv" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");

    a.href = url;
    a.download = title + "_Attendance.csv";
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
</script>
<script>
    function confirmDelete(url) {

    Swal.fire({

        title: 'Delete Table?',

        text: 'This action cannot be undone.',

        icon: 'warning',

        showCancelButton: true,

        confirmButtonColor: '#CB0000',

        cancelButtonText: 'Cancel',

        confirmButtonText: 'Delete'

    }).then((result)=>{


        if(result.isConfirmed){

            window.location.href = url;

        }


    });

}
</script>
    <script>
        function toggleAttendance(button)
{
    const content = button.parentElement.nextElementSibling;
    const arrow = button.querySelector(".arrow");

    content.classList.toggle("hidden");

    if(content.classList.contains("hidden"))
    {
        arrow.style.transform = "rotate(0deg)";
    }
    else
    {
        arrow.style.transform = "rotate(90deg)";
    }
}
    </script>
</body>
</html>
