<?php
include 'db_connect.php'; 
include 'sidebar.php';

// Fetch distinct purposes and laboratories for filtering
$purposes = $conn->query("SELECT DISTINCT purpose FROM sit_in_records");
$labs = $conn->query("SELECT DISTINCT lab FROM sit_in_records");

// Fetch sit-in records with student names
$result = $conn->query("SELECT r.id, r.idno, CONCAT(s.lastname, ', ', s.firstname, ' ', s.middlename) AS name, r.purpose, r.lab, DATE(r.date_time) as date, TIME(r.date_time) as login_time, TIME(r.logout_time) as logout_time FROM sit_in_records r JOIN student s ON r.idno = s.idno WHERE r.logout_time IS NOT NULL ORDER BY r.date_time DESC");


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Reports</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/datetime/1.1.0/css/dataTables.dateTime.min.css">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/datetime/1.1.0/js/dataTables.dateTime.min.js"></script>

    <style>
        .content {
            flex-grow: 1;
            padding: 20px;
        }
        .header {
            background: #112D4E;
            color: #fff;
            padding: 20px 30px;
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .filters {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }
        .filters select, .filters input {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .filters label {
            padding: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        table, th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #112D4E;
            color: white;
        }
        .dt-buttons {
            margin-bottom: 10px;
        }
        @media print {
        .header {
            position: relative;
            text-align: center !important;
            margin: 0 auto 20px auto;
            float: none !important;
            width: 100%;
        }

        body {
            -webkit-print-color-adjust: exact; /* Keep background colors */
            print-color-adjust: exact;
        }
    }
    </style>
</head>
<body>

<?php renderSidebar(); ?>

<div class="content">
    <div class="header">GENERATE REPORTS</div>

    <!-- Filter Options -->
    <div class="filters">
        <select id="filterPurpose">
            <option value="">Filter by Purpose</option>
            <?php while ($row = $purposes->fetch_assoc()) : ?>
                <option value="<?php echo $row['purpose']; ?>"><?php echo $row['purpose']; ?></option>
            <?php endwhile; ?>
        </select>

        <select id="filterLab">
            <option value="">Filter by Laboratory</option>
            <?php while ($row = $labs->fetch_assoc()) : ?>
                <option value="<?php echo $row['lab']; ?>"><?php echo $row['lab']; ?></option>
            <?php endwhile; ?>
        </select>
<!-- 
            <input type="date" id="filterDate"> -->

    </div>

    <table id="sitInRecords" class="display nowrap" style="width:100%">
        <thead>
            <tr>
                <th>ID Number</th>
                <th>Name</th>
                <th>Purpose</th>
                <th>Laboratory</th>
                <th>Date</th>
                <th>Login</th>
                <th>Logout</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?php echo $row['idno']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['purpose']; ?></td>
                    <td><?php echo $row['lab']; ?></td>
                    <td><?php echo $row['date']; ?></td>
                    <td><?php echo $row['login_time']; ?></td>
                    <td><?php echo $row['logout_time'] ? $row['logout_time'] : 'Still Logged In'; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        var table = $('#sitInRecords').DataTable({
            responsive: true,
            ordering: true,
            order: [[4, 'desc']],
            dom: 'Bfrtip',
            buttons: [
                { extend: 'csv', text: '<i class="fas fa-file-csv"></i> CSV', className: 'btn btn-primary' },
                { extend: 'excel', text: '<i class="fas fa-file-excel"></i> Excel', className: 'btn btn-success' },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-danger',
                    title: ' ',
                    filename: 'Sit-in Reports',
                    customize: function(doc) {
                        doc.content.splice(0, 0, {
                            text: [
                                'University of Cebu\n',
                                'College of Computer Studies\n',
                                'Computer Laboratory Sit in Monitoring System Report\n\n'
                            ],
                            margin: [0, 0, 0, 12],
                            alignment: 'center',
                            fontSize: 12,
                            bold: true
                        });
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> Print',
                    className: 'btn btn-info',
                    customize: function (win) {
                        // Add the header at the top center
                        $(win.document.body).prepend(
                            '<div style="text-align: center; margin-bottom: 20px;">' +
                                '<h2 style="font-size: 16pt; font-weight: bold;">University of Cebu</h2>' +
                                '<h3 style="font-size: 14pt;">College of Computer Studies</h3>' +
                                '<h4 style="font-size: 14pt;">Computer Laboratory Sit-in Monitoring System Report</h4>' +
                            '</div>'
                        );

                        // Ensure the table takes up 100% of the width in print view
                        $(win.document.body).find('table').css('width', '100%');

                        // Apply print-specific styling
                        $(win.document.body).css('font-family', 'Arial, sans-serif');
                        $(win.document.body).css('font-size', '12pt');
                        $(win.document.body).find('h2, h3, h4').css({
                            'margin': '0',
                            'padding': '0',
                        });

                        // Add some padding to the body of the printout for spacing
                        $(win.document.body).css('padding', '20px');
                    }
                }



            ]
        });

        // Dropdown filters
        $('#filterPurpose, #filterLab').on('change', function() {
            var purpose = $('#filterPurpose').val();
            var lab = $('#filterLab').val();

            table.column(2).search(purpose).column(3).search(lab).draw();
        });

        //  // Custom search function for filtering by date
        //  $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        //     let selectedDate = $('#filterDate').val();
        //     let recordDate = data[5]; // Column index of Date

        //     if (!selectedDate || recordDate === selectedDate) {
        //         return true;
        //     }
        //     return false;
        // });

        // Apply filter when date changes
        $('#filterDate').on('change', function() {
            table.draw();
        });
    });
</script>

</body>
</html>
