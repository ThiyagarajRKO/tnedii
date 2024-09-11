<style>
.wizard {
  width: 100%;
}
.wizard > .steps .current-info,
.wizard > .content > .title {
  position: absolute;
  left: -99999px;
}
.wizard > .content {
  position: relative;
  width: auto;
  padding: 0;
}
.wizard > .content > .body {
  padding: 0 0px;
}
.wizard > .content > iframe {
  border: 0 none;
  width: 100%;
  height: 100%;
}
.wizard > .steps {
  position: relative;
  display: block;
  width: 100%;
}
.wizard > .steps > ul {
  display: table;
  width: 100%;
  table-layout: fixed;
  margin: 0;
  padding: 0;
  list-style: none;
}
.wizard > .steps > ul > li {
  display: table-cell;
  width: auto;
  vertical-align: top;
  text-align: center;
  position: relative;
}
.wizard > .steps > ul > li a {
  position: relative;
  padding-top: 48px;
  margin-top: 40px;
  margin-bottom: 40px;
  display: block;
}
.wizard > .steps > ul > li:before,
.wizard > .steps > ul > li:after {
  content: '';
  display: block;
  position: absolute;
  top: 58px;
  width: 50%;
  height: 2px;
  background-color: #76BD1D;
  z-index: 9;
}
.wizard > .steps > ul > li:before {
  left: 0;
}
.wizard > .steps > ul > li:after {
  right: 0;
}
.wizard > .steps > ul > li:first-child:before,
.wizard > .steps > ul > li:last-child:after {
  content: none;
}
.wizard > .steps > ul > li.current:after,
.wizard > .steps > ul > li.current ~ li:before,
.wizard > .steps > ul > li.current ~ li:after {
  background-color: #eeeeee;
}
.wizard > .steps > ul > li.current > a {
  color: #76BD1D;
  cursor: default;
}
.wizard > .steps > ul > li.current .number {
  border-color: #76BD1D;
  color: #76BD1D;
}
/*.wizard > .steps > ul > li.current .number:after {
  content: '\e913';
  font-family: 'icomoon';
  display: inline-block;
  font-size: 16px;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  line-height: 34px;
  -webkit-transition: all 0.15s ease-in-out;
  -o-transition: all 0.15s ease-in-out;
  transition: all 0.15s ease-in-out;
}*/
.wizard > .steps > ul > li.disabled a,
.wizard > .steps > ul > li.disabled a:hover,
.wizard > .steps > ul > li.disabled a:focus {
  color: #A5AEB7;
  cursor: default;
}
.wizard > .steps > ul > li.done a,
.wizard > .steps > ul > li.done a:hover,
.wizard > .steps > ul > li.done a:focus {
  color: #76BD1D;
}
.wizard > .steps > ul > li.done .number {
  font-size: 0;
  background-color: #76BD1D;
  border-color: #76BD1D;
  color: #fff;
}
.wizard > .steps > ul > li.done .number:after {
  content: '\2713';
  font-family: 'icomoon';
  display: inline-block;
  font-size: 16px;
  line-height: 34px;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  -webkit-transition: all 0.15s ease-in-out;
  -o-transition: all 0.15s ease-in-out;
  transition: all 0.15s ease-in-out;
}
.wizard > .steps > ul > li.error .number {
  border-color: #F44336;
  color: #F44336;
}
@media (max-width: 768px) {
  .wizard > .steps > ul {
    margin-bottom: 20px;
  }
  .wizard > .steps > ul > li {
    display: block;
    float: left;
    width: 50%;
  }
  .wizard > .steps > ul > li > a {
    margin-bottom: 0;
  }
  .wizard > .steps > ul > li:first-child:before,
  .wizard > .steps > ul > li:last-child:after {
    content: '';
  }
  .wizard > .steps > ul > li:last-child:after {
    background-color: #00BCD4;
  }
}
@media (max-width: 480px) {
  .wizard > .steps > ul > li {
    width: 100%;
  }
  .wizard > .steps > ul > li.current:after {
    background-color: #76BD1D;
  }
}
.wizard > .steps .number {
  background-color: #fff;
  color: #A5AEB7;
  display: inline-block;
  position: absolute;
  top: 0;
  left: 50%;
  margin-left: -19px;
  width: 38px;
  height: 38px;
  border: 2px solid #eeeeee;
  font-size: 14px;
  border-radius: 50%;
  z-index: 10;
  line-height: 34px;
  text-align: center;
}
.panel-flat > .wizard > .steps > ul {
  border-top: 1px solid #ddd;
}
.wizard > .actions {
  position: relative;
  display: block;
  text-align: right;
  padding: 40px 0px;
  padding-top: 20px;
}
.wizard > .actions > ul {
  /*float: left;*/
  list-style: none;
  padding: 0;
  margin: 0;
  display:flex;
  /*justify-content:center*/
}
.wizard > .actions > ul:after {
  content: '';
  display: table;
  clear: both;
}
.wizard > .actions > ul > li {
  /*float: left;*/
}
.wizard > .actions > ul > li + li {
  margin-left: 10px;
}
.wizard > .actions > ul > li > a {
    background: #337ab7;
    color: #fff;
    display: block;
    padding: 10px 25px;
    border-radius: 0px;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 1px;
    border: 0px solid #337ab7;
    font-weight:700;
}
.wizard > .actions > ul > li > a:hover,
.wizard > .actions > ul > li > a:focus {
}
.wizard > .actions > ul > li > a:active {
}
.wizard > .actions > ul > li > a[href="#previous"] {
  background-color: #fff;
  color: #fff;
  border: 0px solid #EDEDED;
}
.wizard > .actions > ul > li > a[href="#previous"]:hover,
.wizard > .actions > ul > li > a[href="#previous"]:focus {

}
.wizard > .actions > ul > li > a[href="#previous"]:active {

}
.wizard > .actions > ul > li.disabled > a,
.wizard > .actions > ul > li.disabled > a:hover,
.wizard > .actions > ul > li.disabled > a:focus {
  background-color: #fff;
  color: #4A4A49;
  border: 0px solid #EDEDED;
}
.wizard > .actions > ul > li.disabled > a[href="#previous"],
.wizard > .actions > ul > li.disabled > a[href="#previous"]:hover,
.wizard > .actions > ul > li.disabled > a[href="#previous"]:focus {
  -webkit-box-shadow: none;
  box-shadow: none;
}
label{
  display:block;
}
label.error{
  color:red;
}
.each-field-box.d-flex{
	gap:10px;
}
.each-field-box.d-flex.gap-30, .gap-30{
	gap:30px;
}
.each-field-box h6{
	margin-bottom:20px;
}
.wizard > .steps .done a, .wizard > .steps .done a:hover, .wizard > .steps .done a:active {
    background: none !important;
    opacity:1;
}
.wizard > .steps .current a, .wizard > .steps .current a:hover, .wizard > .steps .current a:active {
    background: none !important;
}
.contact-form .contact-form-input {
    margin-bottom: 15px;
}
h2 {
   margin-bottom:20px;
}
.d-flex{
    display:flex;
}
input[type=checkbox], input[type=radio] {
    margin: -5px 0 0;
}
</style>

<div class="">
			<div id="" class="row justify-content-center">
				<div id="" class="">
					<div id="jquery-steps">
						<h3>Basic Information</h3>
						<section>
							<h2><strong>Basic Information of the Institution/Incubation Centre</strong></h2>
							<form id="account-form" action="#" class="contact-form">
								<div class="row">
									<div class="col-lg-12 mb-3">
										<div class="each-field-box">
											<label for=""><strong>Name of the Institution</strong></label>
											<input type="text" class="contact-form-input" placeholder="Name">
										</div>
									</div>
									<div class="col-lg-12 mb-3">
										<div class="each-field-box">
											<label for=""><strong>Office Address</strong></label>
											<textarea rows="5" class="contact-form-input" placeholder="Office Address"></textarea>
										</div>
									</div>
									<div class="col-lg-12">
										<div class="row">
										    <div class="col-lg-6 mb-3">
												<label for=""><strong>State</strong></label>
												<select class="contact-form-input">
												    <option>Select State</option>
												</select>
											</div>
											<div class="col-lg-6 mb-3">
												<label for=""><strong>District</strong></label>
												<select class="contact-form-input">
												    <option>Select District</option>
												</select>
											</div>
											
										</div>
									</div>
									<div class="col-lg-6 mb-3">
										<div class="each-field-box">
											<label for=""><strong>Year of establishment/Inception</strong></label>
											<input type="text" class="contact-form-input" placeholder="Year of establishment/Inception">
										</div>
									</div>
									<div class="col-lg-6 mb-3">
										<div class="each-field-box">
											<label for=""><strong>Upload Proof of Registration</strong></label>
											<input type="file" class="contact-form-input" placeholder="Upload Proof of Registration">
										</div>
									</div>
									<div class="col-lg-12">
										<div class="row">
											<div class="col-lg-4 mb-3">
												<label for=""><strong>GST No</strong></label>
												<input type="text" class="contact-form-input" placeholder="GST No">
											</div>
											<div class="col-lg-4 mb-3">
												<label for=""><strong>PAN</strong></label>
												<input type="text" class="contact-form-input" placeholder="PAN">
											</div>
											<div class="col-lg-4 mb-3">
												<label for=""><strong>TIN</strong></label>
												<input type="text" class="contact-form-input" placeholder="TIN">
											</div>
										</div>
									</div>
									<div class="col-lg-6 mb-3">
										<div class="each-field-box">
											<label for=""><strong>Board/Founders/Director</strong></label>
											<input type="text" class="contact-form-input" placeholder="Board/Founders/Director">
										</div>
									</div>
									<div class="col-lg-6 mb-3">
										<div class="each-field-box">
											<label for=""><strong>Years of Experience in Incubation</strong></label>
											<input type="text" class="contact-form-input" placeholder="Years of Experience in Incubation">
										</div>
									</div>
									<div class="col-lg-12 mb-3">
										<div class="each-field-box">
											<label for=""><strong>C.E.O Name</strong></label>
											<input type="text" class="contact-form-input" placeholder="C.E.O Name">
										</div>
									</div>
									<div class="col-lg-12 mb-3">
										<div class="each-field-box">
											<label for=""><strong>Lead Scientist / Incubation Manager</strong></label>
											<input type="text" class="contact-form-input" placeholder="Lead Scientist / Incubation Manager">
										</div>
									</div>
									<div class="col-lg-6 mb-3">
										<div class="each-field-box">
											<label for=""><strong>Years of Experience in R&D </strong></label>
											<input type="text" class="contact-form-input" placeholder="Years of Experience in R&D ">
										</div>
									</div>
									<div class="col-lg-6 mb-3">
										<div class="each-field-box">
											<label for=""><strong>Sources of Financial Support</strong></label>
											<input type="text" class="contact-form-input" placeholder="Sources of Financial Support">
										</div>
									</div>
									<div class="col-lg-12 mb-3">
										<div class="each-field-box">
											<label for=""><strong>Key recognition Award Received by Institute (If any Attach Proof)	<small>(Please upload only jpg or pdf format)</small></strong></label>
											<div class="row">
											<div class="form-group video_container">
                                                <div class="col-sm-11">
                                                  <input type="file" class="contact-form-input" name="award-received-by-institute[]">
                                                </div>
                                                <div class="col-sm-1">
                                                  <button class="btn btn-success video-add">
                                                    <span class="fa fa-plus"></span>
                                                  </button>
                                                </div>
                                              </div>
                                            </div>
										</div>
									</div>
									<div class="col-lg-12 mb-3">
										<div class="each-field-box">
											<label for=""><strong>Certifications (If any) <small>(Please upload only jpg or pdf format)</small></strong></label>
											<div class="row">
    											<div class="form-group video_container">
                                                    <div class="col-sm-11">
                                                      <input type="file" class="contact-form-input" name="certifications[]">
                                                    </div>
                                                    <div class="col-sm-1">
                                                      <button class="btn btn-success video-add">
                                                        <span class="fa fa-plus"></span>
                                                      </button>
                                                    </div>
                                                </div>
                                            </div>
										</div>
									</div>
									<div class="col-lg-12 mb-3">
										<div class="each-field-box">
											<label for=""><strong>Contact Person</strong></label>
											<input type="text" class="contact-form-input" placeholder="Contact person">
										</div>
									</div>
									<div class="col-lg-12">
										<div class="row">
											<div class="col-lg-6 mb-3">
												<label for=""><strong>Mobile Number</strong></label>
												<input type="text" class="contact-form-input" placeholder="Mobile Number">
											</div>
											<div class="col-lg-6 mb-3">
												<label for=""><strong>Email Address</strong></label>
												<input type="text" class="contact-form-input" placeholder="Email address">
											</div>
										</div>
									</div>
									<div class="col-lg-12 mb-3">
										<div class="each-field-box">
											<label for=""><strong>Website</strong></label>
											<input type="text" class="contact-form-input" placeholder="Website">
										</div>
									</div>
								</div>
							</form>
						</section>
						<h3>Sectors</h3>
						<section>
							<h2><strong>Sectors  / core competencies of the institution: (Please tick the appropriate response)</strong></h2>
							<form id="profile-form" class="contact-form">
								<div class="row">
									<div class="col-lg-6">
										<div class="each-field-box d-flex align-items-baseline">
											<input type="checkbox">
											<label for=""><strong>Agriculture</strong></label>
										</div>
									</div>
									<div class="col-lg-6">
										<div class="each-field-box d-flex align-items-baseline">
											<input type="checkbox" >
											<label for=""><strong>Arts/Humanities/Social sciences</strong></label>
										</div>
									</div>
									<div class="col-lg-6">
										<div class="each-field-box d-flex align-items-baseline">
											<input type="checkbox">
											<label for=""><strong>Biotechnology </strong></label>
										</div>
									</div>
									<div class="col-lg-6">
										<div class="each-field-box d-flex align-items-baseline">
											<input type="checkbox" >
											<label for=""><strong>Commerce</strong></label>
										</div>
									</div>
									<div class="col-lg-6">
										<div class="each-field-box d-flex align-items-baseline">
											<input type="checkbox">
											<label for=""><strong>Engineering & Technology</strong></label>
										</div>
									</div>
									<div class="col-lg-6">
										<div class="each-field-box d-flex align-items-baseline">
											<input type="checkbox" >
											<label for=""><strong>E-commerce</strong></label>
										</div>
									</div>
									<div class="col-lg-6">
										<div class="each-field-box d-flex align-items-baseline">
											<input type="checkbox">
											<label for=""><strong>Food Technology</strong></label>
										</div>
									</div>
									<div class="col-lg-6">
										<div class="each-field-box d-flex align-items-baseline">
											<input type="checkbox">
											<label for=""><strong>Health</strong></label>
										</div>
									</div>
									<div class="col-lg-6">
										<div class="each-field-box d-flex align-items-baseline">
											<input type="checkbox">
											<label for=""><strong>Leather Technology</strong></label>
										</div>
									</div>
									<div class="col-lg-6">
										<div class="each-field-box d-flex align-items-baseline">
											<input type="checkbox">
											<label for=""><strong>Marketing </strong></label>
										</div>
									</div>
									<div class="col-lg-6">
										<div class="each-field-box d-flex align-items-baseline">
											<input type="checkbox">
											<label for=""><strong>Medical/Health</strong></label>
										</div>
									</div>
									<div class="col-lg-6">
										<div class="each-field-box d-flex align-items-baseline">
											<input type="checkbox">
											<label for=""><strong>Medical Devices </strong></label>
										</div>
									</div>
									<div class="col-lg-6">
										<div class="each-field-box d-flex align-items-baseline">
											<input type="checkbox">
											<label for=""><strong>Management</strong></label>
										</div>
									</div>
									<div class="col-lg-6">
										<div class="each-field-box d-flex align-items-baseline">
											<input type="checkbox">
											<label for=""><strong>Water and Sanitation</strong></label>
										</div>
									</div>
									<div class="col-lg-6">
										<div class="each-field-box d-flex align-items-baseline mb-3">
											<input type="checkbox" class="other">
											<label for=""><strong>Other (Please Specify)</strong></label>
										</div>
									</div>
									<div class="col-lg-12 other-open" style="margin-top:20px; display:none;">
										<div class="each-field-box">
											<textarea rows="3" class="contact-form-input" placeholder="Please Specify"></textarea>
										</div>
									</div>
								</div>
							</form>
						</section>
						<h3>Research & Development</h3>
						<section>
							<h2><strong>Research & Development</strong></h2>
							<form id="form-1" class="contact-form">
								<div class="row">
									<div class="col-lg-12">
										<div class="each-field-box d-flex gap-30 mb-4">
											<label for=""><strong>Do have testing Lab Facilities</strong></label>
											<div class="each-field-box d-flex align-items-baseline">
												<input type="radio" id="yes" name="lab-facilities" value="Yes">
												<label for=""><strong>Yes</strong></label>
											</div>
											<div class="each-field-box d-flex align-items-baseline">
												<input type="radio" id="no" name="lab-facilities" value="No">
												<label for=""><strong>No</strong></label>
											</div>
										</div>
									</div>
									<div class="col-lg-12 mb-3 lab-facilities-extra" style="display:none">
										<div class="each-field-box">
											<div class="sub-form-field-area">
												<div class="each-field-box d-flex align-items-baseline gap-30 mb-3" style="margin-bottom:20px;">
													<label for="" class="mb-0 w-50"><strong>Type of Lab </strong></label>
													<div class="lab-choose-wrapper d-flex gap-30 w-50">
														<div class="each-field-box d-flex align-items-baseline">
															<input type="radio" id="dry" name="lab_type" value="Dry">
															<label for=""><strong>Dry </strong></label>
														</div>
														<div class="each-field-box d-flex align-items-baseline">
															<input type="radio" id="wet" name="lab_type" value="Wet">
															<label for=""><strong>Wet</strong></label>
														</div>
														<div class="each-field-box d-flex align-items-baseline">
															<input type="radio" id="both" name="lab_type" value="Both">
															<label for=""><strong>Both</strong></label>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-lg-6 mb-3">
    									<div class="each-field-box align-items-center mb-3">
    										<label for="" class="mb-0 w-50"><strong style="white-space: nowrap;">Area in Sq.Ft</strong></label>
    										<input type="text" class="contact-form-input w-50" placeholder="Area in Sq.Ft">
    									</div>
									</div>
									<div class="col-lg-6 mb-3">
    									<div class="each-field-box align-items-center">
    										<label for="" class="mb-0 w-50"><strong>Equipment's</strong></label>
    										<input type="text" class="contact-form-input w-50" placeholder="Enclose as Annexure">
    									</div>
									</div>
									<div class="col-lg-12 mb-3">
										<div class="each-field-box">
											<label for=""><strong>Describe the lab facility which you are ready to share with the innovators on priority basis. (Max 500 Characteristics )</strong></label>
											<textarea rows="5" class="contact-form-input" placeholder="Describe the lab facility"></textarea>
										</div>
									</div>
									<div class="col-lg-12 mb-3">
										<div class="each-field-box">
											<label for=""><strong>What is the service charge you collect from the innovators for the service offered?</strong></label>
											<input type="text" class="contact-form-input" placeholder="service charge you collect">
										</div>
									</div>
									<div class="col-lg-12 mb-3">
										<div class="each-field-box">
											<label for=""><strong>Describe the concessions offered to innovators for using your lab facilities :(Max 500 Characteristics )</strong></label>
											<textarea rows="5" class="contact-form-input" placeholder="Describe the concessions offered"></textarea>
										</div>
									</div>
								</div>
							</form>
						</section>
						<h3>Facilities</h3>
						<section>
							<h2><strong>Facilities available for Innovation/Research</strong></h2>
							<form id="form-2" class="contact-form">
								<div class="row">
									<div class="col-lg-12 mb-3">
										<div class="each-field-box">
											<label for=""><strong>Number of Entrepreneurship awareness/training programs conducted in the last three fiscal years</strong></label>
											<input type="text" class="contact-form-input" placeholder="Entrepreneurship awareness/training programs number">
										</div>
									</div>
									<div class="col-lg-12 mb-3">
										<div class="each-field-box">
											<label for=""><strong>Number of boot camp/ideation/design thinking/business proposal/business pitch workshop conducted (Enclose Reports as Annexure )</strong></label>
											<input type="text" class="contact-form-input" placeholder="boot camp/ideation/design thinking/business proposal/business">
										</div>
									</div>
									<div class="col-lg-12">
										<div class="each-field-box d-flex gap-30 mb-4">
											<label for=""><strong>Have you accelerated/funded startups in the last three fiscal years?</strong></label>
											<div class="each-field-box d-flex align-items-baseline">
												<input type="radio" id="yes" name="three-fiscal-years" value="Yes">
												<label for=""><strong>Yes</strong></label>
											</div>
											<div class="each-field-box d-flex align-items-baseline">
												<input type="radio" id="no" name="three-fiscal-years" value="No">
												<label for=""><strong>No</strong></label>
											</div>
										</div>
									</div>
									<div class="three-fiscal-years-box" style="display:none">
    									<div class="col-lg-8 mb-3">
    										<div class="each-field-box">
    											<div class="sub-form-field-area">
    												<div class="each-field-box">
    													<label for=""><strong>Total number of startups supported in the last three fiscal year</strong></label>
    													<input type="text" class="contact-form-input" placeholder="last three fiscal year">
    												</div>
    											</div>
    										</div>
    									</div>
    									<div class="col-lg-12">
    									    <div class="each-field-box">
    											<label for=""><strong>IVP applications & sanctions in the last 3 years</strong></label>
    											<div class="row">
                									<div class="col-lg-4 mb-3">
                										<input type="text" class="contact-form-input" placeholder="IVP applications & sanctions">
                									</div>
                										<div class="col-lg-4 mb-3">
                										<input type="text" class="contact-form-input" placeholder="IVP applications & sanctions">
                									</div>
                										<div class="col-lg-4 mb-3">
                										<input type="text" class="contact-form-input" placeholder="IVP applications & sanctions">
                									</div>
            									</div>
    										</div>
    									</div>
									</div>
								</div>
							</form>
						</section>
						<h3>Technical support </h3>
						<section>
							<h2><strong>Technical support and mentorship  available in the institution:</strong></h2>
							<form id="account-form" action="#" class="contact-form">
								<div class="row">
									<div class="col-lg-6 mb-3">
										<div class="each-field-box">
											<label for=""><strong>Total no of mentors available </strong></label>
											<input type="text" class="contact-form-input" placeholder="Total no of mentors available">
										</div>
									</div>
									<div class="col-lg-6 mb-3">
										<div class="each-field-box">
											<label for=""><strong>Name</strong></label>
											<input type="text" class="contact-form-input" placeholder="Name">
										</div>
									</div>
									<div class="col-lg-12">
										<div class="row">
											<div class="col-lg-6 mb-3">
												<label for=""><strong>Qualification</strong></label>
												<input type="text" class="contact-form-input" placeholder="Qualification">
											</div>
											<div class="col-lg-6 mb-3">
												<label for=""><strong>Designation</strong></label>
												<input type="text" class="contact-form-input" placeholder="Designation">
											</div>
										</div>
									</div>
									<div class="col-lg-12">
										<div class="row">
											<div class="col-lg-6 mb-3">
												<label for=""><strong>Date of joining your organization</strong></label>
											<input type="text" class="contact-form-input" placeholder="Date of joining your organization">
											</div>
											<div class="col-lg-6 mb-3">
												<label for=""><strong>Number of years experience</strong></label>
											<input type="text" class="contact-form-input" placeholder="Number of years experience">
											</div>
										</div>
									</div>
									<div class="col-lg-12 mb-3">
										<div class="each-field-box">
											<label for=""><strong>How many innovators he/she has guided so far</strong></label>
											<input type="text" class="contact-form-input" placeholder="How many innovators he/she has guided so far">
										</div>
									</div>
								</div>
							</form>
						</section>
						<h3>VI IPR related Registrations </h3>
						<section>
							<h2><strong>IPR related Registrations</strong></h2>
							<form id="account-form" action="#" class="contact-form">
								<div class="row">
									<div class="col-lg-12 mb-3">
										<div class="each-field-box">
											<label for=""><strong>Number of technologies commercialized in past 5 years:(Give Short Description)</strong></label>
											<textarea rows="5" class="contact-form-input" placeholder="Number of technologies commercialized in past 5 years"></textarea>
										</div>
									</div>
									<div class="col-lg-12 mb-3">
										<div class="each-field-box">
											<label for=""><strong>Number of Indian or WIPO-compliant patents received in last 5 years:(Give Short Description)</strong></label>
											<textarea rows="5" class="contact-form-input" placeholder="Number of Indian or WIPO-compliant patents received in last 5 years"></textarea>
										</div>
									</div>
								</div>
							</form>
						</section>
						<h3>VII Financial Support</h3>
						<section>
							<h2><strong>VII Financial support received for innovators <small>(Enclose as Annexure format)</small></strong></h2>
							<form id="account-form" action="#" class="contact-form">
								<div class="row">
									<div class="col-lg-12 mb-3">
									    <div class="row">
											<div class="form-group video_container">
                                                <div class="col-sm-3">
                                                    <div class="each-field-box">
            											<label for="" style="font-size:13px"><strong>Name of the innovator</strong></label>
            										</div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="each-field-box">
            											<label for="" style="font-size:13px"><strong>Contact details</strong></label>
            										</div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="each-field-box">
            											<label for="" style="font-size:13px"><strong>Funds successfully canvassed for them in ₹</strong></label>
            										</div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="each-field-box">
            											<label for="" style="font-size:13px"><strong>Date of funds receipt</strong></label>
            										</div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="each-field-box">
            											<label for="" style="font-size:13px"><strong>Financial Support Received From</strong></label>
            										</div>
                                                </div>
                                                <div class="col-sm-1"></div>
                                            </div>
                                        </div>
									    <div class="row">
											<div class="form-group video_container">
                                                <div class="col-sm-3">
                                                    <div class="each-field-box">
            											<input type="text" class="contact-form-input" placeholder="Name of the innovator">
            										</div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="each-field-box">
            											<input type="text" class="contact-form-input" placeholder="Contact details">
            										</div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="each-field-box">
            											<input type="text" class="contact-form-input" placeholder="Funds successfully canvassed for them in ₹">
            										</div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="each-field-box">
            											<input type="text" class="contact-form-input" placeholder="Date of funds receipt">
            										</div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="each-field-box">
            											<input type="text" class="contact-form-input" placeholder="Financial Support Received From">
            										</div>
                                                </div>
                                                <div class="col-sm-1">
                                                  <button class="btn btn-success video-add">
                                                    <span class="fa fa-plus"></span>
                                                  </button>
                                                </div>
                                            </div>
                                        </div>
										
									</div>
									<div class="col-lg-12 mb-3">
										<div class="each-field-box">
											<label for=""><strong>VIIIPlease let us know your financial status</strong><small>(attach last 3 years balance sheet)</small></label>
											<div class="row">
											    <div class="col-lg-6">
											        <input type="text" class="contact-form-input" placeholder="Last Three Fiscal Year">
											    </div>
											    <div class="col-lg-12"></div>
											    <div class="col-lg-4">
											        <input type="file" class="contact-form-input" >
											    </div>
											    <div class="col-lg-4">
											        <input type="file" class="contact-form-input" >
											    </div>
											    <div class="col-lg-4">
											        <input type="file" class="contact-form-input" >
											    </div>
											</div>
										</div>
									</div>
									<div class="col-lg-6 mb-3">
										<div class="each-field-box">
											<label for=""><strong>IX Land and buildings as on Date</strong></label>
											<input type="date" class="contact-form-input" placeholder="IX Land and buildings as on Date">
										</div>
									</div>
									<div class="col-lg-12">
									    <div class="each-field-box">
									        <label for=""><strong>XKnowledge Partner and InnovatorRelationship</label>
    									    <div class="row">
            									<div class="col-lg-6">
            										<div class="each-field-box d-flex align-items-baseline">
            											<input type="checkbox">
            											<label for=""><strong>Subsidiary</strong></label>
            										</div>
            									</div>
            									<div class="col-lg-6">
            										<div class="each-field-box d-flex align-items-baseline">
            											<input type="checkbox" >
            											<label for=""><strong>Related party entity</strong></label>
            										</div>
            									</div>
            									<div class="col-lg-6">
            										<div class="each-field-box d-flex align-items-baseline">
            											<input type="checkbox">
            											<label for=""><strong>Student </strong></label>
            										</div>
            									</div>
            									<div class="col-lg-6">
            										<div class="each-field-box d-flex align-items-baseline">
            											<input type="checkbox" >
            											<label for=""><strong>Innovator</strong></label>
            										</div>
            									</div>
            									
            								</div>
        								</div>
									</div>
								</div>
							</form>
						</section>
					</div>
				</div>
			</div>
			
		</div>
		<!-- partial -->
		<link media="all" type="text/css" rel="stylesheet" href="/vendor/core/plugins/contact/css/contact-public.css?v=1.0.0">
	    
		