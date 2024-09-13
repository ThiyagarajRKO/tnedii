@extends('layouts.front_app')

<head>
   <!-- MDB -->
   <link href="{{url("plugins/MDB5-7.2.0/css/mdb.min.css?v=2")}}" rel="stylesheet" />
   <link rel="stylesheet" href="{{url("plugins/dataTables/datatables.min.css?v=2")}}" />
   <link rel="stylesheet" href="{{url("plugins/select2/css/select2.min.css?v=2")}}" />

   <!-- Font Awesome -->
   <link rel="stylesheet" href="{{url("plugins/font-awesome/css/all.min.css?v=2")}}" />

</head>

<!-- Bootstrap -->
<!-- <link href="{{url("plugins/bootstrap/css/bootstrap.min.css?v=2")}}" rel="stylesheet" /> -->
<style>
   .header-top-wrapper .header-social ul li a {
      padding-top: 7px;
   }

   .fa-lock:before {
      content: "\f023";
      color: #ff9529;
   }

   /* Modal */
   .modal-content {
      border: 3px solid rgb(213 213 213) !important;
      background: white !important;
      /* box-shadow: 0 30px 30px rgba(0, 0, 0, 0.2); */
   }

   table.dataTable {
      border-radius: 14px;
   }

   /* Table */
   table.dataTable thead th {
      color: white !important;
   }

   .dt-search input[type='search'] {
      height: auto !important
   }

   .dt-length {
      display: flex;
   }

   .dt-length label {
      margin-top: auto;
      margin-bottom: auto;
      /* Adjust spacing between label and input */
   }

   .dt-search {
      display: flex;
   }

   .dt-search label {
      margin-top: auto;
      margin-bottom: auto;
      /* Adjust spacing between label and input */
   }

   .form-control {
      border-radius: 4px !important;
      height: auto !important;
      padding: 5px 10px !important;
   }

   /* Select 2 */
   .select2-dropdown {
      z-index: 10000 !important;
   }

   .select2-selection {
      height: auto !important
   }

   .select2-selection__arrow b {
      margin-top: auto !important;
      margin-bottom: auto !important;
   }

   .select2-selection__clear {
      margin-right: 5px !important;
   }

   .select2-results__options {
      &[aria-multiselectable=true] {

         .select2-results__option {
            &[aria-selected=true]:before {
               content: '☑';
               font-size: 20px;
               padding: 0 6px 0 4px;
            }

            &:before {
               content: '◻';
               font-size: 20px;
               padding: 0 6px 0 4px;
            }
         }
      }
   }
</style>

@section('content')
<!-- Counter Start -->
<div class="section counter-section">
   <div class="container mt-5" style="margin-bottom:100px;">
      <div class="neet-pg-layout">
         <div class="card border">
            <div class="card-body">
               <!-- All India Rank Tab -->
               <div class="tab-pane fade show active" id="all-india-rank" role="tabpanel"
                  aria-labelledby="all-india-rank-tab">
                  <table id="training_applicants" class="table display table-hover nowrap w-100">
                     <thead class="w-auto">
                        <tr>
                           <!--<th>Photo</th>-->
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
         </div>
      </div>
   </div>
</div>
@endsection

@section('script')

<!--jQuery-->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script> 

<!-- MDB -->
<script src="{{url("plugins/MDB5-7.2.0/js/mdb.es.min.js?v=2")}}"></script>

<!-- Bootstrap -->
 <!--<script src="{{url("plugins/bootstrap/js/bootstrap.min.js?v=2")}}"></script> -->

<!-- Datatables -->
<script src="{{url("plugins/dataTables/datatables.min.js?v=2")}}"></script>
<script src="{{url("plugins/dataTables/dataTables.bootstrap5.min.js?v=2")}}"></script>

<!-- Select2 -->
<script src="{{url("plugins/select2/js/select2.min.js?v=2")}}"></script>


<script>
      $("#training_applicants").DataTable({
         destroy: true,
         responsive: false,
         processing: true,
         serverSide: true,
         scrollX: true,
         scrollCollapse: true,
         scrollY: "60vh",
         ordering: false,
         language: {
            searchPlaceholder: 'Global Search'
         },
         ajax: {
            type: "GET",
            url: "{{route('get_districts')}}",
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
</script>
@endsection