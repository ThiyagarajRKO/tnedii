@extends('core/base::layouts.master')

@section('head')
<link rel="stylesheet" href="{{url("plugins/dataTables/datatables.min.css?v=2")}}" />

@endsection

@section('content')
<div class="card border">
   <div class="card-body">
      <!-- Filter Button -->
      <div class="d-flex justify-content-end mb-3">
         <div class="btn btn-outline-primary d-flex justify-content-center rounded align-items-center"
            style="height:35px;border-radius:4px !important" id="download">
            <span><i class="fa fa-download fs-6"></i></span>
            &nbsp;&nbsp;
            <span class="text-sm">Download</span>
         </div>
      </div>
      <!-- Filter Button End -->
      <table id="training_applicants" class="table display table-hover nowrap w-100">
         <thead class="w-auto">
            <tr>
               <th>#</th>
               <th>Photo</th>
               <th>Training Type</th>
               <th>Canditate Type</th>
               <th>Name</th>
               <th>Email address</th>
               <th>Prefix</th>
               <th>Care of</th>
               <th>Father/Mother/Husband Name</th>
               <th>Gender</th>
               <th>Date of birth</th>
               <th>Aadhaar No</th>
               <th>Physically Challenged</th>
               <th>Community</th>
               <th>Qualification</th>
               <th>Address</th>
               <th>District</th>
               <th>Pincode</th>
               <th>Contact No</th>
            </tr>
         </thead>
      </table>
   </div>
</div>
@endsection

@section('javascript')

<!-- Datatables -->
<script src="{{url("plugins/dataTables/datatables.min.js?v=2")}}"></script>
<script src="{{url("plugins/dataTables/dataTables.bootstrap5.min.js?v=2")}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>

<script>
   $("#training_applicants").DataTable({
      destroy: true,
      responsive: false,
      processing: true,
      serverSide: true,
      scrollX: true,
      scrollCollapse: true,
      scrollY: "40vh",
      ordering: false,
      language: {
         searchPlaceholder: 'Global Search'
      },
      ajax: {
         type: "GET",
         url: "{{route('get_training_applicants')}}",
         error: function (xhr) {
            $("#allotments").DataTable().destroy();
            $("#allotments").DataTable({ scrollX: true, ordering: false });
         },
         dataSrc: function (data) {
            data.iTotalRecords = data?.rows?.length || 0;
            data.iTotalDisplayRecords = data.count || 0;
            return data?.rows || [];
         }
      },
      columns: [
         {
            "data": null,
            "defaultContent": "",
            "render": function (data, type, row, meta) {
               return meta.row + 1;
            }
         },
         {
            "data": null,
            "defaultContent": "",
            "render": function (data, type, row, meta) {
               // Construct the image URL, falling back to a default if needed
               const photoUrl = data?.photo
                  ? `{{ asset('/storage/') }}/${data.photo}`
                  : "{{ asset('images/no-image.png') }}";


               // Determine whether the image is the default "no image" placeholder
               const isNoImage = !data?.photo;

               // Create the image element
               const imageHtml = `<img src="${photoUrl}" alt="User Photo" style="width:80px; height:80px"/>`;

               // Return the anchor element conditionally
               return isNoImage
                  ? imageHtml
                  : `<a href="${photoUrl}" target="_blank" aria-label="View User Photo">${imageHtml}</a>`;
            }
         },
         { data: "training_type" },
         { data: "candidate_type" },
         { data: "name" },
         { data: "email" },
         { data: "prefix" },
         { data: "care_of" },
         { data: "father_mother_husband_name" },
         { data: "gender" },
         { data: "date_of_birth" },
         { data: "aadhaar_no" },
         { data: "physically_challenged" },
         { data: "community" },
         { data: "qualification" },
         { data: "address" },
         { data: "district" },
         { data: "pincode" },
         { data: "contact_no" },
      ],

   });


   $(document).on('click', '#download', (e) => {
      e.preventDefault();
      $.ajax({
         url: "{{route('get_training_applicants')}}",
         method: 'GET',
         success: function ({ rows }) {

            // Step 1: Define custom headers
            var headers = [
               'Training Type',
               'Candidate Type',
               'Name',
               'Email',
               'Prefix',
               'Care Of',
               'Father/Mother/Husband Name',
               'Gender',
               'Date of Birth',
               'Aadhaar No',
               'Physically Challenged',
               'Community',
               'Qualification',
               'Address',
               'District',
               'Pincode',
               'Contact No',
               'Photo'
            ];

            // Step 2: Map the JSON data to the custom headers
            var data = rows.map(function (item) {
               return {
                  'Training Type': item.training_type,
                  'Candidate Type': item.candidate_type,
                  'Name': item.name,
                  'Email': item.email,
                  'Prefix': item.prefix,
                  'Care Of': item.care_of,
                  'Father/Mother/Husband Name': item.father_mother_husband_name,
                  'Gender': item.gender,
                  'Date of Birth': item.date_of_birth,
                  'Aadhaar No': item.aadhaar_no,
                  'Physically Challenged': item.physically_challenged,
                  'Community': item.community,
                  'Qualification': item.qualification,
                  'Address': item.address,
                  'District': item.district,
                  'Pincode': item.pincode,
                  'Contact No': item.contact_no,
                  'Photo': item.photo
               };
            });

            // Step 3: Add headers to the data
            data.unshift({
               'Training Type': headers[0],
               'Candidate Type': headers[1],
               'Name': headers[2],
               'Email': headers[3],
               'Prefix': headers[4],
               'Care Of': headers[5],
               'Father/Mother/Husband Name': headers[6],
               'Gender': headers[7],
               'Date of Birth': headers[8],
               'Aadhaar No': headers[9],
               'Physically Challenged': headers[10],
               'Community': headers[11],
               'Qualification': headers[12],
               'Address': headers[13],
               'District': headers[14],
               'Pincode': headers[15],
               'Contact No': headers[16],
               'Photo': headers[17]
            });

            // Step 4: Convert the custom data to a worksheet
            var worksheet = XLSX.utils.json_to_sheet(data, { skipHeader: true });

            // Create a new workbook
            var workbook = XLSX.utils.book_new();

            // Append the worksheet to the workbook
            XLSX.utils.book_append_sheet(workbook, worksheet, "Applicants");

            // Generate the current date and time for the filename
            var now = new Date();
            var timestamp = now.toISOString().replace(/[:.-]/g, ''); // Format as YYYYMMDDTHHMMSS
            var filename = `applicants_${timestamp}.xlsx`;

            // Generate the Excel file and trigger download
            XLSX.writeFile(workbook, filename);
         },
         error: function (xhr, status, error) {
            console.log("Error: " + error);
         }
      });
   })
</script>
@endsection