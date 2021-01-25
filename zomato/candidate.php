<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Candidate extends CI_Controller
{

	public function dashboard()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			$this->load->model('My_model');

			$data['fname']=$this->session->userdata('user_fname');
			$data['user_id']=$this->session->userdata('user_id');

			$user_details=$this->My_model->get_details('user_id', $data['user_id'], 'user');

			$data['lname']=$user_details->lname;
			$data['cpoints']=$this->My_model->get_cpoints($this->session->userdata('user_id'));
			$update_array9=array('cpoints'=>$data['cpoints']);

			$this->My_model->update('user_id', $data['user_id'],'user',$update_array9);

			$user_additional_details=$this->My_model->get_details('user_id', $data['user_id'], 'user_additional_details');

			$data['profile_pic']=$user_additional_details->user_profile_pic;


			//$data['num_active_applications']=$this->My_model->return_num_rows_with_key('user_id', $data['user_id'], 'internship_application');

			//$data['num_tests']=$this->My_model->return_num_rows_with_three_keys('user_id', $data['user_id'], 'payment_status', 'Complete', 'status', 0, 'test_applications');

			//$data['num_internship_offers']=$this->My_model->return_num_rows_with_two_keys('user_id', $data['user_id'], 'application_status', 'Shortlisted', 'internship_application');

			//$data['num_past_internships']=$this->My_model->return_num_rows_with_two_keys('user_id', $data['user_id'], 'application_status', 'Selected', 'internship_application');

			$data['application_info']=$this->My_model->load_job_applications($this->session->userdata('user_id'));

			//$data['interview_info']=$this->My_model->load_interview_offers($this->session->userdata('user_id'));

			//SEO variables
            $data['title']="Nfly Dashboard | ".$data['fname']." ".$data['lname'];

            $data['desc']="Nfly dashboard for the user- ".$data['fname']." ".$data['lname'];
            $data['keyword']="Nfly,dashboard, profile, user, recruitment, platform, placement, job, internship";

            $url="http://nfly.in/testapi/test_result";

			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

			$headers = array('X-Api-Key:59671596837f42d974c7e9dcf38d17e8');

			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

			$payload = Array(
			    'key' => 'user_id',
			    'value' => $this->session->userdata('user_id')
			);

			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));

			$response = curl_exec($ch);
			curl_close($ch);

			//var_dump($response);
			//Kint::dump($response);
			$test_result = json_decode($response, true);
			//print_r($test_result);
			$data['test_result'] = $test_result;

			$data['is_user_logged_in']=1;

			$this->load->view('includes/css_link', $data);
			$this->load->view('includes/headerv1', $data);
			$this->load->view('candidate/view_dashboard', $data);
			$this->load->view('includes/footer');
		}
		else
		{
			redirect('landing');
		}
	}

	public function profile($info_type)
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			if(isset($info_type))
			{
				if($info_type=="personal" || $info_type=="academic" || $info_type=="skills" || $info_type=="personality")
				{
					$this->load->model('My_model');
					$data['fname']=$this->session->userdata('user_fname');
					$data['user_id']=$this->session->userdata('user_id');
					$data['email']=$this->session->userdata('email');

					$user_details=$this->My_model->get_details('user_id', $data['user_id'], 'user');

					$data['lname']=$user_details->lname;
					$data['cpoints']=$this->My_model->get_cpoints($this->session->userdata('user_id'));

					$update_array9=array('cpoints'=>$data['cpoints']);

					$this->My_model->update('user_id', $data['user_id'],'user',$update_array9);


					$user_additional_details=$this->My_model->get_details('user_id', $data['user_id'], 'user_additional_details');

					$data['profile_pic']=$user_additional_details->user_profile_pic;



					if($info_type=="personal")
					{
						$data['designation']=$user_additional_details->user_designation;
						$data['gender']=$user_additional_details->user_gender;
						$data['dob']=$user_additional_details->user_dob;
						$data['city']=$user_additional_details->user_city;
						$data['current_city']=$user_additional_details->user_current_city;
						$data['cover_letter']=$user_additional_details->user_cover_letter;
						$data['address']=$user_additional_details->user_address;
						$data['phone']=$user_additional_details->user_phone;

						$data['language_details']=$this->My_model->load_rows_join_condition('user_language', 'language', 'language_id', 'language_id', 'user_id', $data['user_id']);

						$data['hobby_details']=$this->My_model->load_rows_join_condition('user_hobby', 'hobby', 'hobby_id', 'hobby_id', 'user_id', $data['user_id']);

						if($this->My_model->data_exists('user_id', $data['user_id'], 'user_social_profile'))
						{
							$user_social_profile=$this->My_model->get_details('user_id', $data['user_id'], 'user_social_profile');

							$data['facebook']=$user_social_profile->user_fb;
							$data['linkedin']=$user_social_profile->user_ln;
							$data['twitter']=$user_social_profile->user_tw;
							$data['quora']=$user_social_profile->user_qr;

						}
						else
						{
							$data['facebook']="";
							$data['linkedin']="";
							$data['twitter']="";
							$data['quora']="";
						}

						//logic to display multiple batches starts
	                    $user_language=$this->My_model->load_rows_join_condition('user_language', 'language', 'language_id', 'language_id', 'user_id', $data['user_id']);

	                    $size=count($user_language);

	                    $data['js_string']="[";
	                    $count=0;
	                    foreach($user_language as $row)
	                    {
	                        $count++;
	                        if($count!=$size)
	                        {
	                            $data['js_string']=$data['js_string']."'".$row->language_name."', ";
	                        }
	                        else
	                        {
	                            $data['js_string']=$data['js_string']."'".$row->language_name."'";
	                        }
	                    }

	                    $data['js_string']=$data['js_string']."]";
	                    //logic to print out multiple batches ends

	                    //logic to display multiple interests starts
	                    $user_hobby=$this->My_model->load_rows_join_condition('user_hobby', 'hobby', 'hobby_id', 'hobby_id', 'user_id', $data['user_id']);

	                    $size1=count($user_hobby);

	                    $data['js_string1']="[";
	                    $count1=0;
	                    foreach($user_hobby as $row)
	                    {
	                        $count++;
	                        if($count1!=$size1)
	                        {
	                            $data['js_string1']=$data['js_string1']."'".$row->hobby_name."', ";
	                        }
	                        else
	                        {
	                            $data['js_string1']=$data['js_string1']."'".$row->hobby_name."'";
	                        }
	                    }

	                    $data['js_string1']=$data['js_string1']."]";
	                    //logic to print out multiple interests ends

	                    //SEO variables
			            $data['title']="Nfly Profile- Personal Details | ".$data['fname']." ".$data['lname'];

			            $data['desc']="Personal section of the user profile of ".$data['fname']." ".$data['lname']." containing important personal information about the user like basic details, contact details, birthdate, gender, social profile links, hobbies, languages and cover letter";
			            $data['keyword']="user, profile, personal, name, designation, gender, birthdate, home town, facebook link, linkedin link, twitter link, contact details, email, phone, language, hobby, interest, recruitment, platform, placement, job, internship";

			            $data['is_user_logged_in']=1;

						
						$this->load->view('includes/css_link', $data);
						$this->load->view('includes/headerv1', $data);
						$this->load->view('candidate/view_personal_profile_new', $data);
						$this->load->view('includes/footer');
					}
					else if($info_type=="academic")
					{
						//Work-ex
						if($this->My_model->data_exists('user_id', $data['user_id'], 'user_employment_details'))
						{
							$data['work_ex_present']=1;
							$data['user_employment_details']=$this->My_model->load_rows('user_id',$data['user_id'], 'user_employment_details');

						}
						else
						{
							$data['work_ex_present']=0;
						}

						//grad details
						$user_graduation_details=$this->My_model->get_details('user_id',$data['user_id'], 'user_graduation_details');

						$data['course']=$user_graduation_details->user_course;
						$data['branch']=$user_graduation_details->user_branch;
						$data['college']=$user_graduation_details->user_college;
						$data['class_of']=$user_graduation_details->user_passing_year;
						$data['cgpa']=$user_graduation_details->user_cgpa;
						//schooling details
						//check if entry exists
						if($this->My_model->data_exists('user_id', $data['user_id'], 'user_school_details'))
						{
							$data['school_info_present']=1;
							$user_school_details=$this->My_model->get_details('user_id',$data['user_id'], 'user_school_details');

							$data['xii_school']=$user_school_details->user_xii_school_name;
							$data['xii_board']=$user_school_details->user_xii_board;
							$data['xii_passing_year']=$user_school_details->user_xii_passing_year;
							$data['xii_stream']=$user_school_details->user_xii_stream;
							$data['xii_marks']=$user_school_details->user_xii_marks;
							$data['x_school']=$user_school_details->user_x_school_name;
							$data['x_board']=$user_school_details->user_x_board;
							$data['x_passing_year']=$user_school_details->user_x_passing_year;
							$data['x_marks']=$user_school_details->user_x_marks;

						}
						else
						{
							$data['school_info_present']=0;
							$data['xii_school']="Not Provided";
							$data['xii_board']="Not Provided";
							$data['xii_passing_year']="Not Provided";
							$data['xii_stream']="Not Provided";
							$data['xii_marks']="Not Provided";
							$data['x_school']="Not Provided";
							$data['x_board']="Not Provided";
							$data['x_passing_year']="Not Provided";
							$data['x_marks']="Not Provided";
						}
						
						//training and certification
						if($this->My_model->data_exists('user_id', $data['user_id'], 'user_training_details'))
						{
							$data['training_present']=1;
							$data['training_info']=$this->My_model->load_rows('user_id',$data['user_id'], 'user_training_details');

						}
						else
						{
							$data['training_present']=0;
						}

						//project info
						if($this->My_model->data_exists('user_id', $data['user_id'], 'user_projects'))
						{
							$data['project_present']=1;
							$data['project_info']=$this->My_model->load_rows('user_id',$data['user_id'], 'user_projects');

						}
						else
						{
							$data['project_present']=0;
						}

						//accolade info
						if($this->My_model->data_exists('user_id', $data['user_id'], 'user_accolades'))
						{
							$data['accolade_present']=1;
							$data['user_accolade_details']=$this->My_model->load_rows('user_id',$data['user_id'], 'user_accolades');

						}
						else
						{
							$data['accolade_present']=0;
						}

						//user work sample
						if($this->My_model->data_exists('user_id', $data['user_id'], 'user_work_sample'))
						{
							$user_work_sample_details=$this->My_model->get_details('user_id',$data['user_id'], 'user_work_sample');

							$data['github']=$user_work_sample_details->github_profile;
							$data['playstore']=$user_work_sample_details->playstore_profile;
							$data['blog']=$user_work_sample_details->blog_profile;
							$data['design']=$user_work_sample_details->design_profile;
						}
						else
						{
							$data['github']='';
							$data['playstore']='';
							$data['blog']='';
							$data['design']='';
						}

						//SEO variables
			            $data['title']="Nfly Profile- Academic Details | ".$data['fname']." ".$data['lname'];

			            $data['desc']="Academic section of the user profile of ".$data['fname']." ".$data['lname']." containing important academic and professional information about the user like work experience details, graduation details, school details, project details, accolades and acheivements, work sample, traning and certifications";
			            $data['keyword']="user, profile, academic, name, work experience, college details, school details, recruitment, platform, placement, job, internship";

			            $data['is_user_logged_in']=1;

						$this->load->view('includes/css_link', $data);
						$this->load->view('includes/headerv1', $data);
						$this->load->view('candidate/view_academic_profile1', $data);
						$this->load->view('includes/footer');
					}
					else if($info_type=="skills")
					{
						$data['skill_details']=$this->My_model->load_rows_join_condition('user_skills', 'skills', 'skill_id', 'skill_id', 'user_id', $data['user_id']);

						//logic to display multiple batches starts
	                    $user_language=$this->My_model->load_rows_join_condition('user_language', 'language', 'language_id', 'language_id', 'user_id', $data['user_id']);

	                    $size=count($user_language);

	                    $data['js_string']="[";
	                    $count=0;
	                    foreach($user_language as $row)
	                    {
	                        $count++;
	                        if($count!=$size)
	                        {
	                            $data['js_string']=$data['js_string']."'".$row->language_name."', ";
	                        }
	                        else
	                        {
	                            $data['js_string']=$data['js_string']."'".$row->language_name."'";
	                        }
	                    }

	                    $data['js_string']=$data['js_string']."]";
	                    //logic to print out multiple batches ends

	                    //logic to display multiple skills starts
	                    $user_skill=$this->My_model->load_rows_join_condition('user_skills', 'skills', 'skill_id', 'skill_id', 'user_id', $data['user_id']);

	                    $size1=count($user_skill);

	                    $data['js_string1']="[";
	                    $count1=0;
	                    foreach($user_skill as $row)
	                    {
	                        $count++;
	                        if($count1!=$size1)
	                        {
	                            $data['js_string1']=$data['js_string1']."'".$row->skill_name."', ";
	                        }
	                        else
	                        {
	                            $data['js_string1']=$data['js_string1']."'".$row->skill_name."'";
	                        }
	                    }

	                    $data['js_string1']=$data['js_string1']."]";
	                    //logic to print out multiple interests ends

	                    //SEO variables
			            $data['title']="Nfly Profile- Skill Details | ".$data['fname']." ".$data['lname'];

			            $data['desc']="Skills section of the user profile of ".$data['fname']." ".$data['lname']." displaying skills that the user possess";
			            $data['keyword']="user, profile, skill, recruitment, platform, placement, job, internship";

			            $data['is_user_logged_in']=1;


						$this->load->view('includes/css_link', $data);
						$this->load->view('includes/headerv1', $data);
						$this->load->view('candidate/view_skill_profile1', $data);
						$this->load->view('includes/footer');	
					}
					else
					{
						if($this->My_model->data_exists('user_id', $data['user_id'], 'user_big5'))
						{
							$data['personality_test_taken']=1;
							$personality_details=$this->My_model->get_details('user_id', $data['user_id'], 'user_big5');
							$data['extra']=$personality_details->extraversion;
							$data['open']=$personality_details->openness;
							$data['con']=$personality_details->conscientiousness;
							$data['neuro']=$personality_details->neuroticism;
							$data['agree']=$personality_details->agreeableness;

							$data['extra_range']=(($data['extra']-8)/32)*100;
							$data['open_range']=(($data['open']-10)/40)*100;
							$data['con_range']=(($data['con']-9)/36)*100;
							$data['neuro_range']=(($data['neuro']-8)/32)*100;
							$data['agree_range']=(($data['agree']-9)/36)*100;
						}
						else
						{
							$data['personality_test_taken']=0;
						}

						//SEO variables
			            $data['title']="Nfly Profile- Personality Details | ".$data['fname']." ".$data['lname'];

			            $data['desc']="Personality section of the user profile of ".$data['fname']." ".$data['lname']." containing important details about the user's personality";
			            $data['keyword']="user, profile, personal, name,personality, big5, assessment, platform, placement, job, internship";

			            $data['is_user_logged_in']=1;

						
						$this->load->view('includes/css_link', $data);
						$this->load->view('includes/headerv1', $data);
						$this->load->view('candidate/view_personality_profile1', $data);
						$this->load->view('includes/footer');	
					}
					
				}
				else
				{
					redirect('error');
				}
			}
			else
			{
				redirect('error');
			}
		}
		else
		{
			redirect('landing');
		}
	}

	public function update_basic_info()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			if(isset($_POST['designation']))
			{
				$this->load->model('My_model');
				$update_array=array('user_designation'=>$this->input->post('designation'),
									'user_gender'=>$this->input->post('gender'),
									'user_dob'=>$this->input->post('dob'),
									'user_current_city'=>$this->input->post('ccity'),
									'user_city'=>$this->input->post('ncity'));

				$insert_array=array('user_id'=>$this->session->userdata('user_id'),
									'user_designation'=>$this->input->post('designation'),
									'user_gender'=>$this->input->post('gender'),
									'user_dob'=>$this->input->post('dob'),
									'user_current_city'=>$this->input->post('ccity'),
									'user_city'=>$this->input->post('ncity'));

				if($this->My_model->data_exists('user_id', $this->session->userdata('user_id'), 'user_additional_details'))
				{
					if($this->My_model->update('user_id', $this->session->userdata('user_id'), 'user_additional_details', $update_array))
					{
						$this->session->set_flashdata('msg','<h5 class="text-success"><i class="fas fa-check"></i>&nbsp&nbspBasic information updated successfully</h5>');
						redirect('candidate/profile/personal');
					}
					else
					{
						$this->session->set_flashdata('msg','<h5 class="text-danger"><i class="fas fa-times"></i>&nbsp&nbspSome error occured. Kindly try again</h5>');
						redirect('candidate/profile/personal');
					}
				}
				else
				{
					if($this->My_model->insert($insert_array, 'user_additional_details'))
					{
						$this->session->set_flashdata('msg','<h5 class="text-success"><i class="fas fa-check"></i>&nbsp&nbspBasic information inserted successfully</h5>');
						redirect('candidate/profile/personal');
					}
					else
					{
						$this->session->set_flashdata('msg','<h5 class="text-danger"><i class="fas fa-times"></i>&nbsp&nbspSome error occured. Kindly try again</h5>');
						redirect('candidate/profile/personal');

					}
				}
			}
			else
			{
				redirect('error');
			}
		}
		else
		{
			redirect('error');
		}
	}

	public function update_contact_info()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			if(isset($_POST['phone']))
			{
				$this->load->model('My_model');
				$update_array=array('user_phone'=>$this->input->post('phone'),
									'user_address'=>$this->input->post('address'));

				if($this->My_model->update('user_id', $this->session->userdata('user_id'), 'user_additional_details', $update_array))
				{
					$this->session->set_flashdata('msg','<h5 class="text-success"><i class="fas fa-check"></i>&nbsp&nbspContact information updated successfully</h5>');
					redirect('candidate/profile/personal');
				}
				else
				{
					$this->session->set_flashdata('msg','<h5 class="text-danger"><i class="fas fa-times"></i>&nbsp&nbspSome error occured. Kindly try again</h5>');
					redirect('candidate/profile/personal');
				}
			}
			else
			{
				redirect('error');
			}
		}
		else
		{
			redirect('error');
		}
	}

	public function update_social_info()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			if(isset($_POST['facebook']))
			{
				$this->load->model('My_model');
				if($this->My_model->data_exists('user_id', $this->session->userdata('user_id'), 'user_social_profile'))
				{
					$update_array=array('user_fb'=>$this->input->post('facebook'),
									'user_ln'=>$this->input->post('linkedin'),
									'user_tw'=>$this->input->post('twitter'),
									'user_qr'=>$this->input->post('quora'));

					if($this->My_model->update('user_id', $this->session->userdata('user_id'), 'user_social_profile', $update_array))
					{
						$this->session->set_flashdata('msg','<h5 class="text-success"><i class="fas fa-check"></i>&nbsp&nbspSocial information updated successfully</h5>');
						redirect('candidate/profile/personal');
					}
					else
					{
						$this->session->set_flashdata('msg','<h5 class="text-danger"><i class="fas fa-times"></i>&nbsp&nbspSome error occured. Kindly try again</h5>');
					redirect('candidate/profile/personal');
					}
				}
				else
				{
					$insert_array=array('user_id'=>$this->session->userdata('user_id'),
						'user_fb'=>$this->input->post('facebook'),
									'user_ln'=>$this->input->post('linkedin'),
									'user_tw'=>$this->input->post('twitter'),
									'user_qr'=>$this->input->post('quora'));

					if($this->My_model->insert($insert_array, 'user_social_profile'))
					{
						$this->session->set_flashdata('msg','complete');
						redirect('candidate/profile/personal');
					}
					else
					{
						$this->session->set_flashdata('msg','incomplete');
						redirect('candidate/profile/personal');
					}
				}
			}
			else
			{
				redirect('error');
			}
		}
		else
		{
			redirect('error');
		}
	}

	public function update_language()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			if(isset($_POST['language']))
			{
				$this->load->model('My_model');
				//$this->My_model->delete_with_one('user_id', $this->session->userdata('user_id'), 'user_language');

                //check if language is already listed
                $row=$this->input->post('language');
                {
                    if(!($this->My_model->data_exists('language_name', $row, 'language')))
                    {
                        //not listed
                        //converting into correct format eg. Kolkata
                        $first_alpha=substr($row,0,1);
                        $first_alpha_upp=strtoupper($first_alpha);
                        $name_rem=substr($row, 1);
                        $name_rem_low=strtolower($name_rem);

                        $language_name=$first_alpha_upp.$name_rem_low;

                        $insert=array('language_name'=>$language_name);
                        $language_id=$this->My_model->insert_with_id('language', $insert);

                    }
                    else
                    {
                        //fetch id of skill
                        $language_details=$this->My_model->get_details('language_name', $row, 'language');
                        $language_id=$language_details->language_id;
                    }

                    $insert_array=array('user_id'=>$this->session->userdata('user_id'),
                        'language_id'=>$language_id);

                    $this->My_model->insert($insert_array, 'user_language');
                }
                $this->session->set_flashdata('msg','complete');
				redirect('candidate/profile/personal');
			}
			else
			{
				redirect('error');
			}
		}
		else
		{
			redirect('error');
		}
	}

	public function update_skill()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			if(isset($_POST['skill']))
			{
				$this->load->model('My_model');
				//$this->My_model->delete_with_one('user_id', $this->session->userdata('user_id'), 'user_skills');

                //check if language is already listed
                foreach($this->input->post('skill') as $row)
                {
                    if(!($this->My_model->data_exists('skill_name', $row, 'skills')))
                    {
                        //not listed
                        //converting into correct format eg. Kolkata
                        $first_alpha=substr($row,0,1);
                        $first_alpha_upp=strtoupper($first_alpha);
                        $name_rem=substr($row, 1);
                        $name_rem_low=strtolower($name_rem);

                        $skill_name=$first_alpha_upp.$name_rem_low;

                        $insert=array('skill_name'=>$skill_name);
                        $skill_id=$this->My_model->insert_with_id('skills', $insert);

                    }
                    else
                    {
                        //fetch id of skill
                        $skill_details=$this->My_model->get_details('skill_name', $row, 'skills');
                        $skill_id=$skill_details->skill_id;
                    }

                    $insert_array=array('user_id'=>$this->session->userdata('user_id'),
                        'skill_id'=>$skill_id);

                    $this->My_model->insert($insert_array, 'user_skills');
                }
                $this->session->set_flashdata('msg','complete');
				redirect('candidate/profile/skills');
			}
			else
			{
				redirect('error');
			}
		}
		else
		{
			redirect('error');
		}
	}

	public function update_interest()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			if(isset($_POST['interest']))
			{
				$this->load->model('My_model');
				//$this->My_model->delete_with_one('user_id', $this->session->userdata('user_id'), 'user_hobby');

                //check if language is already listed
                foreach($this->input->post('interest') as $row)
                {
                    if(!($this->My_model->data_exists('hobby_name', $row, 'hobby')))
                    {
                        //not listed
                        //converting into correct format eg. Kolkata
                        $first_alpha=substr($row,0,1);
                        $first_alpha_upp=strtoupper($first_alpha);
                        $name_rem=substr($row, 1);
                        $name_rem_low=strtolower($name_rem);

                        $hobby_name=$first_alpha_upp.$name_rem_low;

                        $insert=array('hobby_name'=>$hobby_name);
                        $hobby_id=$this->My_model->insert_with_id('hobby', $insert);

                    }
                    else
                    {
                        //fetch id of skill
                        $hobby_details=$this->My_model->get_details('hobby_name', $row, 'hobby');
                        $hobby_id=$hobby_details->hobby_id;
                    }

                    $insert_array=array('user_id'=>$this->session->userdata('user_id'),
                        'hobby_id'=>$hobby_id);

                    $this->My_model->insert($insert_array, 'user_hobby');
                }
                $this->session->set_flashdata('msg','complete');
				redirect('candidate/profile/personal');
			}
			else
			{
				redirect('error');
			}
		}
		else
		{
			redirect('error');
		}
	}

	public function update_cover_info()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			if(isset($_POST['cover']))
			{
				$this->load->model('My_model');
				$update_array=array('user_cover_letter'=>$this->input->post('cover'));

				if($this->My_model->update('user_id', $this->session->userdata('user_id'), 'user_additional_details', $update_array))
				{
					$this->session->set_flashdata('msg','<h5 class="text-success"><i class="fas fa-check"></i>&nbsp&nbspCover letter updated successfully!</h5>');
					redirect('candidate/profile/personal');
				}
				else
				{
					$this->session->set_flashdata('msg','<h5 class="text-danger"><i class="fas fa-times"></i>&nbsp&nbspSome error occured. Kindly try again</h5>');
					redirect('candidate/profile/personal');
				}
				
			}
			else
			{
				redirect('error');
			}
		}
		else
		{
			redirect('error');
		}
	}

	public function update_phone()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			$this->load->model('My_model');
			$update_array=array('user_phone'=>$this->input->post('phone'));

			if($this->My_model->update('user_id', $this->input->post('user_id'), 'user_additional_details', $update_array))
			{
				$this->session->set_flashdata('msg','Phone number updated successfully!');
				redirect('candidate/profile');
			}
			else
			{
				$this->session->set_flashdata('msg','Some error occured. Kindly try again!');
				redirect('candidate/profile');
			}
		}
		else
		{
			redirect('landing');
		}
	}

	public function update_phone2()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			$this->load->model('My_model');
			$update_array=array('user_phone'=>$this->input->post('phone'));

			if($this->My_model->update('user_id', $this->input->post('user_id'), 'user_additional_details', $update_array))
			{
				$this->session->set_flashdata('apply_msg','Phone number updated successfully!');
				redirect('internship/view_internship/'.$this->input->post('internship_url'));
			}
			else
			{
				$this->session->set_flashdata('apply_msg','Some error occured. Kindly try again!');
				redirect('internship/view_internship/'.$this->input->post('internship_url'));
			}
		}
		else
		{
			redirect('landing');
		}
	}

	public function update_city()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			$this->load->model('My_model');
			$update_array=array('user_city'=>$this->input->post('city'));

			if($this->My_model->update('user_id', $this->input->post('user_id'), 'user_additional_details', $update_array))
			{
				$this->session->set_flashdata('msg','City updated successfully!');
				redirect('candidate/profile');
			}
			else
			{
				$this->session->set_flashdata('msg','Some error occured. Kindly try again!');
				redirect('candidate/profile');
			}
		}
		else
		{
			redirect('landing');
		}
	}

	public function add_workex()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			$this->load->model('My_model');
			$insert_array=array('user_id'=>$this->input->post('user_id'),
								'user_job_type'=>$this->input->post('job_type'),
								'user_company'=>$this->input->post('company_name'),
								'user_job_profile'=>$this->input->post('job_profile'),
								'user_job_start_date'=>$this->input->post('start_date'),
								'user_job_end_date'=>$this->input->post('last_date'),
								'user_job_desc'=>$this->input->post('job_desc'));

			if($this->My_model->insert($insert_array, 'user_employment_details'))
			{
				$this->session->set_flashdata('msg','<h5 class="text-success"><i class="fas fa-check"></i>&nbsp&nbspWork experience added successfully!</h5>');
				redirect('candidate/profile/academic');
			}
			else
			{
				$this->session->set_flashdata('msg','<h5 class="text-danger"><i class="fas fa-times"></i>&nbsp&nbspSome error occured. Kindly try again</h5>');
				redirect('candidate/profile');
			}
		}
		else
		{
			redirect('landing');
		}
	}

	public function add_accolade()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			$this->load->model('My_model');
			$insert_array=array('user_id'=>$this->input->post('user_id'),
								'accolade_title'=>$this->input->post('title'),
								'accolade_details'=>$this->input->post('desc'));

			if($this->My_model->insert($insert_array, 'user_accolades'))
			{
				$this->session->set_flashdata('msg','<h5 class="text-success"><i class="fas fa-check"></i>&nbsp&nbspAccolade added successfully!</h5>');
				redirect('candidate/profile/academic');
			}
			else
			{
				$this->session->set_flashdata('msg','<h5 class="text-danger"><i class="fas fa-times"></i>&nbsp&nbspSome error occured. Kindly try again</h5>');
				redirect('candidate/profile/academic');
			}
		}
		else
		{
			redirect('landing');
		}
	}

	public function edit_accolade()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			$this->load->model('My_model');
			$update_array=array('user_id'=>$this->input->post('user_id'),
								'accolade_title'=>$this->input->post('title'),
								'accolade_details'=>$this->input->post('desc'));

			if($this->My_model->update('ua_id', $this->input->post('id'), 'user_accolades', $update_array))
			{
				$this->session->set_flashdata('msg','<h5 class="text-success"><i class="fas fa-check"></i>&nbsp&nbspAccolade updated successfully!</h5>');
				redirect('candidate/profile');
			}
			else
			{
				$this->session->set_flashdata('msg','<h5 class="text-danger"><i class="fas fa-times"></i>&nbsp&nbspSome error occured. Kindly try again</h5>');
				redirect('candidate/profile');
			}
		}
		else
		{
			redirect('landing');
		}
	}

	public function edit_workex($temp)
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			$this->load->model('My_model');

			if($temp==1)
			{
				$update_array=array('user_id'=>$this->input->post('user_id'),
								'user_company'=>$this->input->post('company_name'));
			}
			elseif($temp==2)
			{
				$update_array=array('user_id'=>$this->input->post('user_id'),
								'user_job_profile'=>$this->input->post('job_profile'));
			}
			elseif($temp==3)
			{
				$update_array=array('user_id'=>$this->input->post('user_id'),
								'user_job_type'=>$this->input->post('job_type'));
			}
			elseif($temp==4)
			{
				$update_array=array('user_id'=>$this->input->post('user_id'),
								'user_job_start_date'=>$this->input->post('start_date'));
			}
			elseif($temp==5)
			{
				$update_array=array('user_id'=>$this->input->post('user_id'),
								'user_job_end_date'=>$this->input->post('last_date'));
			}
			elseif($temp==6)
			{
				$update_array=array('user_id'=>$this->input->post('user_id'),
								'user_job_desc'=>$this->input->post('job_desc'));
			}


			if($this->My_model->update('ued_id', $this->input->post('work_ex_id'), 'user_employment_details', $update_array))
			{
				$this->session->set_flashdata('msg','<h5 class="text-success"><i class="fas fa-check"></i>&nbsp&nbspWork experience updated successfully!</h5>');
				//redirect('candidate/profile/academic');
				//var_dump($update_array);
				print_r($update_array);
			}
			else
			{
				$this->session->set_flashdata('workex_msg','Some error occured. Kindly try again!');
				redirect('candidate/profile/academic');
			}
		}
		else
		{
			redirect('landing');
		}
	}

	public function add_worksample()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			$this->load->model('My_model');
			if($this->My_model->data_exists('user_id', $this->input->post('user_id'), 'user_work_sample'))
			{
				$update_array=array('github_profile'=>$this->input->post('github'),
								'playstore_profile'=>$this->input->post('playstore'),
								'blog_profile'=>$this->input->post('blog'),
								'design_profile'=>$this->input->post('design'));

				if($this->My_model->update('user_id', $this->input->post('user_id'), 'user_work_sample', $update_array))
				{
					$this->session->set_flashdata('msg','<h5 class="text-success"><i class="fas fa-check"></i>&nbsp&nbspWork sample added successfully!</h5>');
					redirect('candidate/profile/academic');
				}
				else
				{
					$this->session->set_flashdata('msg','<h5 class="text-danger"><i class="fas fa-times"></i>&nbsp&nbspSome error occured. Kindly try again</h5>');
					redirect('candidate/profile/academic');
				}
			}
			else
			{
				$insert_array=array('user_id'=>$this->input->post('user_id'),
								'github_profile'=>$this->input->post('github'),
								'playstore_profile'=>$this->input->post('playstore'),
								'blog_profile'=>$this->input->post('blog'),
								'design_profile'=>$this->input->post('design'));

				if($this->My_model->insert($insert_array, 'user_work_sample'))
				{
					$this->session->set_flashdata('msg','complete');
					redirect('candidate/profile/academic');
				}
				else
				{
					$this->session->set_flashdata('msg','<h5 class="text-danger"><i class="fas fa-times"></i>&nbsp&nbspSome error occured. Kindly try again</h5>');
					redirect('candidate/profile/academic');
				}
			}
			
		}
		else
		{
			redirect('landing');
		}
	}

	public function add_training()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			if(isset($_POST['company']))
			{
				$this->load->model('My_model');
				$insert_array=array('user_id'=>$this->input->post('user_id'),
									'user_training_course'=>$this->input->post('course'),
									'user_training_company'=>$this->input->post('company'),
									'user_training_duration'=>$this->input->post('duration'),
									'user_training_details'=>$this->input->post('desc'));

				if($this->My_model->insert($insert_array, 'user_training_details'))
				{
					$this->session->set_flashdata('msg','<h5 class="text-success"><i class="fas fa-check"></i>&nbsp&nbspTraining added successfully!</h5>');
					redirect('candidate/profile/academic');	
				}
				else
				{
					$this->session->set_flashdata('msg','<h5 class="text-danger"><i class="fas fa-times"></i>&nbsp&nbspSome error occured. Kindly try again</h5>');
					redirect('candidate/profile/academic');
				}
			}
			else
			{
				redirect('error');
			}
		}
		else
		{
			redirect('landing');
		}
	}


	public function add_project()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			if(isset($_POST['name']))
			{
				$this->load->model('My_model');
				$insert_array=array('user_id'=>$this->input->post('user_id'),
									'user_project_name'=>$this->input->post('name'),
									'user_project_details'=>$this->input->post('desc'),
									'user_project_link'=>$this->input->post('link'));

				if($this->My_model->insert($insert_array, 'user_projects'))
				{
					$this->session->set_flashdata('msg','<h5 class="text-success"><i class="fas fa-check"></i>&nbsp&nbspProject added successfully!</h5>');
					redirect('candidate/profile/academic');	
				}
				else
				{
					$this->session->set_flashdata('msg','<h5 class="text-danger"><i class="fas fa-times"></i>&nbsp&nbspSome error occured. Kindly try again</h5>');
					redirect('candidate/profile/academic');
				}
			}
			else
			{
				redirect('landing');
			}
		}
		else
		{
			redirect('landing');
		}
	}

	public function edit_project()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			if(isset($_POST['name']))
			{
				$this->load->model('My_model');
				$update_array=array('user_id'=>$this->input->post('user_id'),
									'user_project_name'=>$this->input->post('name'),
									'user_project_details'=>$this->input->post('desc'),
									'user_project_link'=>$this->input->post('link'));

				if($this->My_model->update('up_id', $this->input->post('project_id'), 'user_projects', $update_array))
				{
					$this->session->set_flashdata('msg','<h5 class="text-success"><i class="fas fa-check"></i>&nbsp&nbspProject updated successfully!</h5>');
					redirect('candidate/profile');	
				}
				else
				{
					$this->session->set_flashdata('msg','<h5 class="text-danger"><i class="fas fa-times"></i>&nbsp&nbspSome error occured. Kindly try again</h5>');
					redirect('candidate/profile');
				}
			}
			else
			{
				redirect('landing');
			}
		}
		else
		{
			redirect('landing');
		}
	}

	public function edit_training()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			if(isset($_POST['company']))
			{
				$this->load->model('My_model');
				$update_array=array('user_id'=>$this->input->post('user_id'),
									'user_training_course'=>$this->input->post('course'),
									'user_training_company'=>$this->input->post('company'),
									'user_training_duration'=>$this->input->post('duration'));

				if($this->My_model->update('utd_id', $this->input->post('training_id'), 'user_training_details', $update_array))
				{
					$this->session->set_flashdata('msg','<h5 class="text-success"><i class="fas fa-check"></i>&nbsp&nbspTraining updated successfully!</h5>');
					redirect('candidate/profile');	
				}
				else
				{
					$this->session->set_flashdata('msg','<h5 class="text-danger"><i class="fas fa-times"></i>&nbsp&nbspSome error occured. Kindly try again</h5>');
					redirect('candidate/profile');
				}
			}
			else
			{
				redirect('landing');
			}
		}
		else
		{
			redirect('landing');
		}
	}

	public function edit_college()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			if(isset($_POST['college']))
			{
				$this->load->model('My_model');
				$update_array=array('user_id'=>$this->input->post('user_id'),
									'user_course'=>$this->input->post('course'),
									'user_branch'=>$this->input->post('branch'),
									'user_college'=>$this->input->post('college'),
									'user_passing_year'=>$this->input->post('passing_year'),
									'user_cgpa'=>$this->input->post('cgpa'));

				if($this->My_model->update('user_id', $this->input->post('user_id'), 'user_graduation_details', $update_array ))
				{
					$this->session->set_flashdata('msg','<h5 class="text-success"><i class="fas fa-check"></i>&nbsp&nbspCollege details updated successfully!</h5>');
					redirect('candidate/profile/academic');
				}
				else
				{
					$this->session->set_flashdata('msg','<h5 class="text-danger"><i class="fas fa-times"></i>&nbsp&nbspSome error occured. Kindly try again</h5>');
					redirect('candidate/profile/academic');
				}
			}
			else
			{
				redirect('landing');
			}
		}
		else
		{
			redirect('landing');
		}
	}

	public function submit_graduation_details()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			if(isset($_POST['course']))
			{
				$update_array=array('user_id'=>$this->input->post('user_id'),
									'user_type'=>$this->input->post('user_type'),
									'user_course'=>$this->input->post('course'),
									'user_branch'=>$this->input->post('branch'),
									'user_college'=>$this->input->post('college'),
									'user_current_year'=>$this->input->post('year'),
									'user_passing_year'=>$this->input->post('pass_year'));

				$this->load->model('My_model');
				if($this->My_model->update('user_id', $this->input->post('user_id'), 'user_graduation_details', $update_array))
				{
					$this->session->set_flashdata('col_msg','College info updated successfully!');
					redirect('candidate/profile');
				}
				else
				{
					$this->session->set_flashdata('col_msg','Some error occured!');
					redirect('candidate/profile');
				}
				
			}
			else
			{
				redirect('landing');
			}
		}
		else
		{
			redirect('landing');
		}
	}

	/*public function submit_class_x_details()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			if($_POST['school_name'])
			{
				$update_array=array('user_id'=>$this->input->post('user_id'),
									'user_x_board'=>$this->input->post('board'),
									'user_x_school_name'=>$this->input->post('school_name'),
									'user_x_marks'=>$this->input->post('marks'),
									'user_x_passing_year'=>$this->input->post('passing_year'));

				$this->load->model('My_model');
				if($this->My_model->update('user_id', $this->input->post('user_id'), 'user_school_details', $update_array))
				{
					$this->session->set_flashdata('schx_msg','Class Xth info updated successfully!');
					redirect('candidate/profile');
				}
				else
				{
					$this->session->set_flashdata('schx_msg','Some error occured!');
					redirect('candidate/profile');
				}
			}
			else
			{
				redirect('landing');
			}
		}
		else
		{
			redirect('landing');
		}
	}*/


	public function edit_school_details()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			if(isset($_POST['xii_school']))
			{
				$this->load->model('My_model');
				//check if data exists
				if($this->My_model->data_exists('user_id', $this->session->userdata('user_id'), 'user_school_details'))
				{
					$update_array=array(
									'user_xii_board'=>$this->input->post('xii_board'),
									'user_xii_school_name'=>$this->input->post('xii_school'),
									'user_xii_stream'=>$this->input->post('xii_stream'),
									'user_xii_marks'=>$this->input->post('xii_marks'),
									'user_xii_passing_year'=>$this->input->post('xii_year'),
									'user_x_board'=>$this->input->post('x_board'),
									'user_x_school_name'=>$this->input->post('x_school'),
									'user_x_marks'=>$this->input->post('x_marks'),
									'user_x_passing_year'=>$this->input->post('x_year'));

				
					if($this->My_model->update('user_id', $this->input->post('user_id'), 'user_school_details', $update_array))
					{
						$this->session->set_flashdata('msg','<h5 class="text-success"><i class="fas fa-check"></i>&nbsp&nbspSchool details updated successfully!</h5>');
						redirect('candidate/profile/academic');
					}
					else
					{
						$this->session->set_flashdata('msg','<h5 class="text-danger"><i class="fas fa-times"></i>&nbsp&nbspSome error occured. Kindly try again</h5>');
						redirect('candidate/profile/academic');
					}
				}
				else
				{
					$insert_array=array('user_id'=>$this->input->post('user_id'),
									'user_xii_board'=>$this->input->post('xii_board'),
									'user_xii_school_name'=>$this->input->post('xii_school'),
									'user_xii_stream'=>$this->input->post('xii_stream'),
									'user_xii_marks'=>$this->input->post('xii_marks'),
									'user_xii_passing_year'=>$this->input->post('xii_year'),
									'user_x_board'=>$this->input->post('x_board'),
									'user_x_school_name'=>$this->input->post('x_school'),
									'user_x_marks'=>$this->input->post('x_marks'),
									'user_x_passing_year'=>$this->input->post('x_year'));

				
					if($this->My_model->insert($insert_array, 'user_school_details'))
					{
						$this->session->set_flashdata('msg','complete');
						redirect('candidate/profile/academic');
					}
					else
					{
						$this->session->set_flashdata('msg','<h5 class="text-danger"><i class="fas fa-times"></i>&nbsp&nbspSome error occured. Kindly try again</h5>');
						redirect('candidate/profile/academic');
					}
				}
			}
			else
			{
				redirect('error');
			}
		}
		else
		{
			redirect('landing');
		}
	}

	public function delete_workex()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			if(isset($_POST['user_id']))
			{
				$this->load->model('My_model');
				if($this->My_model->delete_with_two('ued_id', $this->input->post('workex_id'), 'user_id', $this->input->post('user_id'), 'user_employment_details'))
				{
					$this->session->set_flashdata('msg','<h5 class="text-success"><i class="fas fa-check"></i>&nbsp&nbspWork experience deleted successfully!</h5>');
					redirect('candidate/profile/academic');
				}
				else
				{
					$this->session->set_flashdata('msg','<h5 class="text-danger"><i class="fas fa-times"></i>&nbsp&nbspSome error occured. Kindly try again</h5>');
					redirect('candidate/profile/academic');
				}
			}
			else
			{
				redirect('landing');
			}
		}
		else
		{
			redirect('landing');
		}
	}

	public function delete_project()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			if(isset($_POST['user_id']))
			{
				$this->load->model('My_model');
				if($this->My_model->delete_with_two('up_id', $this->input->post('project_id'), 'user_id', $this->input->post('user_id'), 'user_projects'))
				{
					$this->session->set_flashdata('msg','<h5 class="text-success"><i class="fas fa-check"></i>&nbsp&nbspProject deleted successfully!</h5>');
					redirect('candidate/profile/academic');
				}
				else
				{
					$this->session->set_flashdata('msg','<h5 class="text-danger"><i class="fas fa-times"></i>&nbsp&nbspSome error occured. Kindly try again</h5>');
					redirect('candidate/profile/academic');
				}
			}
			else
			{
				redirect('landing');
			}
		}
		else
		{
			redirect('landing');
		}
	}


	public function delete_training()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			if(isset($_POST['user_id']))
			{
				$this->load->model('My_model');
				if($this->My_model->delete_with_two('utd_id', $this->input->post('training_id'), 'user_id', $this->input->post('user_id'), 'user_training_details'))
				{
					$this->session->set_flashdata('msg','<h5 class="text-success"><i class="fas fa-check"></i>&nbsp&nbspTraining deleted successfully!</h5>');
					redirect('candidate/profile/academic');
				}
				else
				{
					$this->session->set_flashdata('msg','<h5 class="text-danger"><i class="fas fa-times"></i>&nbsp&nbspSome error occured. Kindly try again</h5>');
					redirect('candidate/profile/academic');
				}
			}
			else
			{
				redirect('landing');
			}
		}
		else
		{
			redirect('landing');
		}
	}

public function delete_accolade()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			if(isset($_POST['user_id']))
			{
				$this->load->model('My_model');
				if($this->My_model->delete_with_two('ua_id', $this->input->post('accolade_id'), 'user_id', $this->input->post('user_id'), 'user_accolades'))
				{
					$this->session->set_flashdata('msg','<h5 class="text-success"><i class="fas fa-check"></i>&nbsp&nbspAccolade deleted successfully!</h5>');
					redirect('candidate/profile/academic');
				}
				else
				{
					$this->session->set_flashdata('msg','<h5 class="text-danger"><i class="fas fa-times"></i>&nbsp&nbspSome error occured. Kindly try again</h5>');
					redirect('candidate/profile/academic');
				}
			}
			else
			{
				redirect('landing');
			}
		}
		else
		{
			redirect('landing');
		}
	}

	public function delete_language()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			if(isset($_POST['user_id']))
			{
				$this->load->model('My_model');
				if($this->My_model->delete_with_two('ul_id', $this->input->post('ul_id'), 'user_id', $this->input->post('user_id'), 'user_language'))
				{
					$this->session->set_flashdata('msg','<h5 class="text-success"><i class="fas fa-check"></i>&nbsp&nbspLanguage deleted successfully!</h5>');
					redirect('candidate/profile/personal');
				}
				else
				{
					$this->session->set_flashdata('msg','<h5 class="text-danger"><i class="fas fa-times"></i>&nbsp&nbspSome error occured. Kindly try again</h5>');
					redirect('candidate/profile/personal');
				}
			}
			else
			{
				redirect('landing');
			}
		}
		else
		{
			redirect('landing');
		}
	}


	public function delete_hobby()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			if(isset($_POST['user_id']))
			{
				$this->load->model('My_model');
				if($this->My_model->delete_with_two('uh_id', $this->input->post('uh_id'), 'user_id', $this->input->post('user_id'), 'user_hobby'))
				{
					$this->session->set_flashdata('msg','<h5 class="text-success"><i class="fas fa-check"></i>&nbsp&nbspHobby deleted successfully!</h5>');
					redirect('candidate/profile/personal');
				}
				else
				{
					$this->session->set_flashdata('msg','<h5 class="text-danger"><i class="fas fa-times"></i>&nbsp&nbspSome error occured. Kindly try again</h5>');
					redirect('candidate/profile/personal');
				}
			}
			else
			{
				redirect('landing');
			}
		}
		else
		{
			redirect('landing');
		}
	}

	public function add_skills()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			if(isset($_POST['user_id']))
			{
				$this->load->model('My_model');
				//check if skill is already listed
				foreach($this->input->post('skill') as $row)
				{
					if(!($this->My_model->data_exists('skill_name', $row, 'skills')))
					{
						//not listed
						//converting into correct format eg. Php
						$first_alpha=substr($row,0,1);
						$first_alpha_upp=strtoupper($first_alpha);
						$name_rem=substr($row, 1);
						$name_rem_low=strtolower($name_rem);

						$skill_name=$first_alpha_upp.$name_rem_low;

						$insert=array('skill_name'=>$skill_name);
						$skill_id=$this->My_model->insert_with_id('skills', $insert);
	
					}
					else
					{
						//fetch id of skill
						$skill_details=$this->My_model->get_details('skill_name', $row, 'skills');
						$skill_id=$skill_details->skill_id;
					}

					$insert_array=array('user_id'=>$this->input->post('user_id'),
										'skill_id'=>$skill_id);

					if(!($this->My_model->data_exists_with_two('user_id', $this->input->post('user_id'), 'skill_id', $skill_id , 'user_skills')))
					{
						$this->My_model->insert($insert_array, 'user_skills');
					}

					
					
	
				}
				$this->session->set_flashdata('skill_msg','Skills updated successfully!');
				redirect('candidate/profile');
			}
			else
			{
				redirect('landing');
			}
		}
		else
		{
			redirect('landing');
		}
	}

	public function delete_skill()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			if(isset($_POST['user_id']))
			{
				$this->load->model('My_model');
				if($this->My_model->delete_with_two('us_id', $this->input->post('us_id'), 'user_id', $this->input->post('user_id'), 'user_skills'))
				{
					$this->session->set_flashdata('msg','<h5 class="text-success"><i class="fas fa-check"></i>&nbsp&nbspSkill deleted successfully!</h5>');
					redirect('candidate/profile/skills');
				}
				else
				{
					$this->session->set_flashdata('msg','<h5 class="text-danger"><i class="fas fa-times"></i>&nbsp&nbspSome error occured. Kindly try again</h5>');
					redirect('candidate/profile/skills');
				}
			}
			else
			{
				redirect('landing');
			}
		}
		else
		{
			redirect('landing');
		}
	}

	public function settings()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			$this->load->model('My_model');
			$data['fname']=$this->session->userdata('user_fname');
			$data['user_id']=$this->session->userdata('user_id');

			$this->load->view('candidate/includes/css_link');
			$this->load->view('candidate/includes/header', $data);
			$this->load->view('candidate/view_settings', $data);
			$this->load->view('candidate/includes/footer');
		}
		else
		{
			redirect('landing');
		}
	}

	public function update_password()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			if(isset($_POST['old']))
			{
				$this->load->model('My_model');
				if($this->My_model->data_exists_two('user_id', $this->input->post('user_id'), 'password', md5($this->input->post('old')), 'user'))
				{
					$update_array=array('password'=>md5($this->input->post('new')));

					if($this->My_model->update('user_id', $this->input->post('user_id'), 'user', $update_array))
					{
						$this->session->set_flashdata('settings_msg','Password updated successfully!');
						redirect('candidate/settings');
					}
					else
					{
						$this->session->set_flashdata('settings_msg','Some error occured. Kindly try again!');
						redirect('candidate/settings');
					}
				}
				else
				{
					$this->session->set_flashdata('settings_msg','The password mentioned is incorrect!');
					redirect('candidate/settings');
				}
			}
			else
			{
				redirect('landing');
			}
		}
		else
		{
			redirect('landing');
		}
	}

	public function apply_for_test($test_id, $internship_url)
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			$this->load->model('My_model');
			$num_test_rows=$this->My_model->return_num_rows_with_key('user_id', $this->session->userdata('user_id'), 'test_applications');
			if($num_test_rows>=2)
			{
				$this->session->set_flashdata('apply_msg','You can not apply for more that two domain tests!');
				redirect('internship/view_internship/'.$internship_url);
			}
			else
			{
				$insert_array=array('test_id'=>$test_id,
									'user_id'=>$this->session->userdata('user_id'),
									'time_left'=>5400);
				
				if($this->My_model->insert($insert_array, 'test_applications'))
				{
					
					$this->session->set_flashdata('apply_msg','You have successfully applied for the test.');
					redirect('internship/view_internship/'.$internship_url);
				}
				else
				{
					$this->session->set_flashdata('apply_msg','Some error occured. Kindly try again!');
					redirect('internship/view_internship/'.$internship_url);

				}
			}
			
		}
		else
		{
			redirect('landing');
		}
	}

	public function get_skills()
    {
        if($this->session->userdata('is_user_logged_in'))
        {
        	$this->load->model('My_model');
            $this->My_model->fetch_skills();
        }
        else
        {
            redirect('Error/page_not_found');
        }
        
    }



	public function get_languages()
    {
        if($this->session->userdata('is_user_logged_in'))
        {
        	$this->load->model('My_model');
            $this->My_model->fetch_languages();
        }
        else
        {
            redirect('Error/page_not_found');
        }
        
    }

    public function get_interests()
    {
        if($this->session->userdata('is_user_logged_in'))
        {
        	$this->load->model('My_model');
            $this->My_model->fetch_interests();
        }
        else
        {
            redirect('Error/page_not_found');
        }
        
    }

    public function update_pic()
    {
    	if($this->session->userdata('is_user_logged_in'))
        {
        	if(isset($_FILES['pp']))
        	{
        		$this->load->model('My_model');
        		$filename=$_FILES['pp']['name'];
        		$filesize=$_FILES['pp']['size']/1024;
        		$tmp_name=$_FILES['pp']['tmp_name'];

        		$ext=substr($filename, strpos($filename, ".")+1);

        		if($ext=="jpg" || $ext=="png")
        		{
        			if($ext<=512)
        			{
    					move_uploaded_file($tmp_name, './assets/images/profile_pic/'.$filename);
    					$update_array=array('user_profile_pic'=>$filename);
    					if($this->My_model->update('user_id', $this->session->userdata('user_id'), 'user_additional_details', $update_array))
    					{
			                $this->session->set_flashdata('pic_msg','Profile picture updated');
    						redirect('candidate/profile/personal');
    					}
        			}
        			else
        			{
		                $this->session->set_flashdata('pic_msg','File size exceeded');
        				redirect('candidate/dashboard');
        			}
        		}
        		else
        		{
	                $this->session->set_flashdata('pic_msg','Incorrect file type');
        			redirect('candidate/dashboard');
        		}
        	}
        	else
        	{
        		redirect('error');
        	}
    		
        }
        else
        {
        	redirect('error');
        }
    }

    public function resume()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			define('FPDF_FONTPATH',APPPATH .'plugins/font/');
			require(APPPATH .'plugins/fpdf.php');
			 
		    $pdf = new FPDF('p','mm','A4');
			$pdf -> AddPage();
			 
			$pdf -> setDisplayMode ('fullpage');

			//Name of the student
			$this->load->Model('My_model');
			$user_details=$this->My_model->get_details('user_id', $this->session->userdata('user_id'), 'user');//to load personal info
			$fname=$user_details->fname;
			$lname=$user_details->lname;
			$email=$user_details->email;

			$user_additional_details=$this->My_model->get_details('user_id', $this->session->userdata('user_id'), 'user_additional_details');//to load personal info
			$phone=$user_additional_details->user_phone;
			$hometown=$user_additional_details->user_city;
			$cover_letter=$user_additional_details->user_cover_letter;

			//get college details
			$user_graduation_details=$this->My_model->get_details('user_id', $this->session->userdata('user_id'), 'user_graduation_details');
			$passing_year=$user_graduation_details->user_passing_year;
			$course=$user_graduation_details->user_course;
			$branch=$user_graduation_details->user_branch;
			$college=$user_graduation_details->user_college;
			$cgpa=$user_graduation_details->user_cgpa;

			$pdf -> setFont ('Arial','B',17);
			$pdf -> cell(0,6,$fname." ".$lname,0,0);

			//phone
			$pdf -> setFont ('Arial','',13);
			$pdf -> cell(0,6,'Phone:'.$phone,0,1, 'R');

			//Year and course
			$pdf -> setFont ('Arial','',13);
			$pdf -> cell(0,6,$course,0,0);

			//Email
			$pdf -> setFont ('Arial','',13);
			$pdf -> cell(0,6,'Email: '.$email,0,1, 'R');

			//Branch
			$pdf -> setFont ('Arial','',13);
			$pdf -> cell(0,6, $branch,0,0);

			//Address
			$pdf -> setFont ('Arial','',13);
			$pdf -> cell(0,6, 'http://nfly.in',0,1, 'R');

			//College
			$pdf -> setFont ('Arial','',13);
			$pdf -> cell(0,6, $college,0,0);

			//City
			$pdf -> setFont ('Arial','',13);
			$pdf -> cell(0,6,'Hometown: '.$hometown,0,1, 'R');

			//space
			$pdf -> cell(0,6,'',0,1);

			//Career Objective
			$pdf -> setFont ('Arial','',13);
			$pdf->SetFillColor(34, 45, 50);
			$pdf->SetTextColor(255, 255, 255);
			$pdf -> cell(0,6,'Career Objective',1,1,'',1);

			//career objective para
			$pdf -> setFont ('Arial','',13);
			$pdf->SetTextColor(0, 0, 0);
			// Output justified text
			$pdf -> cell(0,3,'',0,1);//space
			$pdf -> write (5, $cover_letter);

			//space
			$pdf -> cell(0,10,'',0,1);

			//Academic Details
			$pdf -> setFont ('Arial','',13);
			$pdf->SetFillColor(34, 45, 50);
			$pdf->SetTextColor(255, 255, 255);
			$pdf -> cell(0,6,'Academic Details',1,1,'',1);

			$pdf->SetTextColor(0, 0, 0);
			//space
			$pdf -> cell(0,5,'',0,1);
			$pdf->cell(25,7,'Class Of',1, 'C');
			$pdf->cell(60,7,'Degree',1);
			$pdf->cell(70,7,'Institute',1);
			$pdf->cell(35,7,'Marks(in %)',1);
			$pdf->Ln();
			$pdf -> cell(0,2,'',0,1);
			$pdf -> setFont ('Arial','',12);


			
		    $pdf->cell(25,12,$passing_year,1);
		  	$pdf->MultiCell( 60, 6, $course.' '.$branch,1);
		  	$pdf->__currentY=$pdf->GetY();
		  	$pdf->SetXY($pdf->GetX()+85, $pdf->__currentY-12);
		  	$pdf->MultiCell( 70, 6, $college,1);
		  	$pdf->SetXY($pdf->GetX()+155, $pdf->__currentY-12);
		  	$pdf->cell(35,12,$cgpa,1);
		  	$pdf->Ln();
		  	$pdf -> cell(0,2,'',0,1);

			//load school info

			if($this->My_model->data_exists('user_id', $this->session->userdata('user_id'), 'user_school_details'))
			{
				//set flag
				$data['school_info_exists']=1;
				//if yes send cgpa
				$user_school_info=$this->My_model->get_details('user_id', $this->session->userdata('user_id'), 'user_school_details');
				$user_xii_school=$user_school_info->user_xii_school_name;
				$user_xii_board=$user_school_info->user_xii_board;
				$user_xii_score=$user_school_info->user_xii_marks;
				$user_xii_year=$user_school_info->user_xii_passing_year;
				$user_x_school=$user_school_info->user_x_school_name;
				$user_x_board=$user_school_info->user_x_board;
				$user_x_score=$user_school_info->user_x_marks;
				$user_x_year=$user_school_info->user_x_passing_year;

				$pdf->cell(25,10, $user_xii_year,1);
				$pdf->MultiCell( 60, 10, 'Class XII- '.$user_xii_board,1);
				$pdf->__currentY=$pdf->GetY();
				$pdf->SetXY($pdf->GetX()+85, $pdf->__currentY-10);
				$pdf->MultiCell( 70, 10, $user_xii_school,1);
				$pdf->SetXY($pdf->GetX()+155, $pdf->__currentY-10);
				$pdf->cell(35,10,$user_xii_score,1);
				$pdf->Ln();
				$pdf -> cell(0,2,'',0,1);

				$pdf->cell(25,10,$user_x_year,1);
				$pdf->MultiCell( 60, 10, 'Class X- '.$user_x_board,1);
				$pdf->__currentY=$pdf->GetY();
				$pdf->SetXY($pdf->GetX()+85, $pdf->__currentY-10);
				$pdf->MultiCell( 70, 10, $user_x_school,1);
				$pdf->SetXY($pdf->GetX()+155, $pdf->__currentY-10);
				$pdf->cell(35,10,$user_x_score,1);
				$pdf->Ln();
				$pdf -> cell(0,7,'',0,1);
				

			}

			//Work experience
			if($this->My_model->data_exists('user_id', $this->session->userdata('user_id'), 'user_employment_details'))
			{
				$user_work_ex=$this->My_model->load_rows('user_id', $this->session->userdata('user_id'), 'user_employment_details');
				//Work Experience
				$count=1;
				$pdf -> setFont ('Arial','',13);
				$pdf->SetFillColor(34, 45, 50);
				$pdf->SetTextColor(255, 255, 255);
				$pdf -> cell(0,6,'Work Experience',1,1,'',1);
				$pdf -> cell(0,5,'',0,1);
				foreach($user_work_ex as $row)
				{
				  $pdf->SetTextColor(0, 0, 0);
				  $pdf->SetFillColor(255, 255, 255);

				  $pdf -> setFont ('Arial','B',13);
				  $pdf -> cell(0,6,$count.'. '.$row->user_job_profile,0,0);
				  $pdf -> setFont ('Arial','B',11);
				  $pdf -> cell(0,6,'Duration: 2 years 4 months',0,1, 'R');
				  $pdf -> setFont ('Arial','I',12);
				  $pdf -> cell(0,6,$row->user_company,0,0);
				  $pdf -> cell(0,7,'',0,1);
				  $pdf -> setFont ('Arial','',11);
				  $pdf -> write (5,$row->user_job_desc);
				  $pdf -> cell(0,10,'',0,1);
				  $count++;


				}

			}

			//Certification and training
			if($this->My_model->data_exists('user_id', $this->session->userdata('user_id'), 'user_training_details'))
			{
				$pdf -> setFont ('Arial','',13);
				$pdf->SetFillColor(34, 45, 50);
				$pdf->SetTextColor(255, 255, 255);
				$pdf -> cell(0,6,'Training & Certifications',1,1,'',1);
				$pdf -> cell(0,5,'',0,1);

				$count=1;

				$user_certificates=$this->My_model->load_rows('user_id', $this->session->userdata('user_id'), 'user_training_details');
				foreach($user_certificates as $row)
				{
					$pdf->SetTextColor(0, 0, 0);
					$pdf->SetFillColor(255, 255, 255);

					$pdf -> setFont ('Arial','B',13);
					$pdf -> cell(0,6,$count.'. '.$row->user_training_course,0,0);
					$pdf -> setFont ('Arial','B',11);
					$pdf -> cell(0,6,'Duration: '.$row->user_training_duration." Months",0,1, 'R');
					$pdf -> setFont ('Arial','I',12);
					$pdf -> cell(0,6,'Certified By: '.$row->user_training_company,0,0);
					$pdf -> cell(0,7,'',0,1);
					$pdf -> setFont ('Arial','',11);
					$pdf -> write (5, $row->user_training_details);
					$pdf -> cell(0,10,'',0,1);

					$count++;

				}
			}

			//Accolades
			if($this->My_model->data_exists('user_id', $this->session->userdata('user_id'), 'user_accolades'))
			{
				$pdf -> setFont ('Arial','',13);
				$pdf->SetFillColor(34, 45, 50);
				$pdf->SetTextColor(255, 255, 255);
				$pdf -> cell(0,6,'Accolades and Acheivments',1,1,'',1);
				$pdf -> cell(0,5,'',0,1);

				$count=1;

				$user_accolades=$this->My_model->load_rows('user_id', $this->session->userdata('user_id'), 'user_accolades');

				foreach($user_accolades as $row)
				{
					$pdf->SetTextColor(0, 0, 0);
					$pdf->SetFillColor(255, 255, 255);

					$pdf -> setFont ('Arial','B',13);
					$pdf -> cell(0,6, $count.'. '.$row->accolade_title,0,1);
					$pdf -> setFont ('Arial','',11);
					$pdf -> write (5, $row->accolade_details);
					$pdf -> cell(0,10,'',0,1);
					$count++;

				}
			}

			//space
			$pdf -> cell(0,10,'',0,1);

			  

			//Skills
			if($this->My_model->data_exists('user_id', $this->session->userdata('user_id'), 'user_skills'))
			{
				$pdf -> setFont ('Arial','',13);
				$pdf->SetFillColor(34, 45, 50);
				$pdf->SetTextColor(255, 255, 255);
				$pdf -> cell(0,6,'Skills',1,1,'',1);
				$pdf -> cell(0,5,'',0,1);
				$pdf->SetTextColor(0, 0, 0);

				$count=1;

				$user_skill=$user_skill=$this->My_model->load_rows_join_condition('user_skills', 'skills', 'skill_id', 'skill_id', 'user_id', $this->session->userdata('user_id'));;
				foreach($user_skill as $row)
				{
					$pdf -> setFont ('Arial','B',10);
					$pdf -> cell(0,6,$count.'. '.$row->skill_name,0,0);
					$pdf -> cell(0,7,'',0,1);

				}
			}	
			  

			  

			//Projects
			if($this->My_model->data_exists('user_id', $this->session->userdata('user_id'), 'user_projects'))
			{
				$pdf -> setFont ('Arial','',13);
				$pdf->SetFillColor(34, 45, 50);
				$pdf->SetTextColor(255, 255, 255);
				$pdf -> cell(0,6,'Projects',1,1,'',1);
				$pdf -> cell(0,5,'',0,1);
				$pdf->SetTextColor(0, 0, 0);

				$count=1;

				$user_projects=$this->My_model->load_rows('user_id', $this->session->userdata('user_id'), 'user_projects');
				foreach($user_projects as $row)
				{
					$pdf->SetTextColor(0, 0, 0);
					$pdf->SetFillColor(255, 255, 255);

					$pdf -> setFont ('Arial','B',13);
					$pdf -> cell(0,6,$count.'. '.$row->user_project_name,0,0);
					if($row->user_project_link!='')
					{
						$pdf -> setFont ('Arial','B',11);
						$pdf -> cell(0,6,'Link:'.$row->user_project_link ,0,1, 'R');
					}
					
					$pdf -> write (5,$row->user_project_details);
					$pdf -> cell(0,10,'',0,1);
					$count++;

				}
			}

			  $pdf -> output ();
		}
		else
		{
			redirect('error');
		}     
	}

		//created by palash
	public function resume2()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			define('FPDF_FONTPATH',APPPATH .'plugins/font/');
			require(APPPATH .'plugins/fpdf.php');
			 
		    $pdf = new FPDF('p','mm','A4');
			$pdf -> AddPage();
			 
			$pdf -> setDisplayMode ('fullpage');
			$pdf->AddFont('RobotoSlab','B','RobotoSlab.php');
			
			//Name of the student
			$this->load->Model('My_model');
			$user_details=$this->My_model->get_details('user_id', $this->session->userdata('user_id'), 'user');//to load personal info
			$fname=$user_details->fname;
			$lname=$user_details->lname;
			$email=$user_details->email;

			$user_additional_details=$this->My_model->get_details('user_id', $this->session->userdata('user_id'), 'user_additional_details');//to load personal info
			$phone=$user_additional_details->user_phone;
			$hometown=$user_additional_details->user_city;
			$cover_letter=$user_additional_details->user_cover_letter;

			//get college details
			$user_graduation_details=$this->My_model->get_details('user_id', $this->session->userdata('user_id'), 'user_graduation_details');
			$passing_year=$user_graduation_details->user_passing_year;
			$course=$user_graduation_details->user_course;
			$branch=$user_graduation_details->user_branch;
			$college=$user_graduation_details->user_college;
			$cgpa=$user_graduation_details->user_college;

			//Name phone email adress
			$pdf->SetFont('RobotoSlab','B',20);
		    $pdf->SetTextColor(45, 98, 183);
		    $pdf->Cell(0, 0, $fname." ".$lname, 0, 0, 'C');
		    $pdf->ln(5);
		    $pdf->SetFont('Arial','B',10);
		    $pdf->SetTextColor(255,255,255);
		    $pdf->SetFillColor(45, 98, 183);
		    $pdf->MultiCell(0, 10, 'Phone:'.$phone.'	'.'Email: '.$email.'	Hometown: '.$hometown, 0,'C',True);
		    $pdf->ln(10);
		    $yl1=$pdf->GetY()-2;

		    $pdf->SetFont('Arial','B',15);
		    $pdf->SetTextColor(45, 98, 183);
		    $y1=$pdf->GetY()+2;
		    $pdf->MultiCell(65,10, "Academic Details", 0,'L');
		    $pdf->SetY($y1);
		    $pdf->ln(5);

			if($this->My_model->data_exists('user_id', $this->session->userdata('user_id'), 'user_school_details'))
			{
				//set flag
				$data['school_info_exists']=1;
				//if yes send cgpa
				$user_school_info=$this->My_model->get_details('user_id', $this->session->userdata('user_id'), 'user_school_details');
				$user_xii_school=$user_school_info->user_xii_school_name;
				$user_xii_board=$user_school_info->user_xii_board;
				$user_xii_score=$user_school_info->user_xii_marks;
				$user_xii_year=$user_school_info->user_xii_passing_year;
				$user_x_school=$user_school_info->user_x_school_name;
				$user_x_board=$user_school_info->user_x_board;
				$user_x_score=$user_school_info->user_x_marks;
				$user_x_year=$user_school_info->user_x_passing_year;
				$pdf->SetX(65);
				$pdf->SetFont('Arial','B',13);
		      	$pdf->SetTextColor(0, 0, 0);
		      	$pdf->MultiCell(0, 4,"College:".$college, 0,'L');

				$y1=$pdf->GetY()+2;
			    $pdf->SetLineWidth(0.5);
			    $pdf->Line(60,$yl1,60,$y1);
		      	$pdf->ln(1);
		      	$pdf->SetX(65);
		      	$pdf->SetFont('RobotoSlab','B',11);
		      	$pdf->MultiCell(0, 4, "Branch:".$branch, 0,'L');
		      	$y1=$pdf->GetY()+2;
			    $pdf->SetLineWidth(0.5);
			    $pdf->Line(60,$yl1,60,$y1);
		      	
		      	$pdf->ln(1);
		      	$pdf->SetX(65);
		      	$pdf->MultiCell(0, 4, "Course:".$course, 0,'L');
		      	$y1=$pdf->GetY()+2;
			    $pdf->SetLineWidth(0.5);
			    $pdf->Line(60,$yl1,60,$y1);

		      	$pdf->ln(1);
		      	$pdf->SetX(65);
		      	$pdf->MultiCell(0, 4, "Year:".$passing_year, 0,'L');
		      	$y1=$pdf->GetY()+2;
			    $pdf->SetLineWidth(0.5);
			    $pdf->Line(60,$yl1,60,$y1);

		      	$pdf->ln(1);
		      	$pdf->SetX(65);
		      	$pdf->MultiCell(0, 4, "CGPA:".$cgpa, 0,'L');
		      	$y1=$pdf->GetY()+2;
			    $pdf->SetLineWidth(0.5);
			    $pdf->Line(60,$yl1,60,$y1);
		      	$pdf->ln(3);


				$pdf->SetX(65);
		      	$pdf->SetFont('Arial','B',13);
		      	$pdf->SetTextColor(0, 0, 0);
		      	$pdf->MultiCell(0, 4,"School:".$user_xii_school, 0,'L');
		      	$y1=$pdf->GetY()+2;
			    $pdf->SetLineWidth(0.5);
			    $pdf->Line(60,$yl1,60,$y1);
		      	$pdf->ln(1);
		      	$pdf->SetX(65);
		      	$pdf->SetFont('RobotoSlab','B',11);
		      	$pdf->MultiCell(0, 4, "Board:".$user_xii_board, 0,'L');
		      	$y1=$pdf->GetY()+2;
			    $pdf->SetLineWidth(0.5);
			    $pdf->Line(60,$yl1,60,$y1);
		      	$pdf->ln(1);
		      	$pdf->SetX(65);
		      	$pdf->MultiCell(0, 4, "Year:".$user_xii_year, 0,'L');
		      	$y1=$pdf->GetY()+2;
			    $pdf->SetLineWidth(0.5);
			    $pdf->Line(60,$yl1,60,$y1);
		      	$pdf->ln(1);
		      	$pdf->SetX(65);
		      	$pdf->MultiCell(0, 4, "Score:".$user_xii_score, 0,'L');
		      	$y1=$pdf->GetY()+2;
			    $pdf->SetLineWidth(0.5);
			    $pdf->Line(60,$yl1,60,$y1);
		      	$pdf->ln(3);

		      	$pdf->SetFont('Arial','B',13);
		      	$pdf->SetX(65);
		      	$pdf->MultiCell(0, 4, "School:".$user_x_school, 0,'L');
		      	$y1=$pdf->GetY()+2;
			    $pdf->SetLineWidth(0.5);
			    $pdf->Line(60,$yl1,60,$y1);
		      	$pdf->ln(1);
		      	$pdf->SetX(65);
		      	$pdf->SetFont('RobotoSlab','B',11);
		      	$pdf->MultiCell(0, 4, "Board:".$user_x_board, 0,'L');
		      	$y1=$pdf->GetY()+2;
			    $pdf->SetLineWidth(0.5);
			    $pdf->Line(60,$yl1,60,$y1);
		      	$pdf->ln(1);
		      	$pdf->SetX(65);
		      	$pdf->MultiCell(0, 4, "Year:".$user_x_year, 0,'L');
		      	$y1=$pdf->GetY()+2;
			    $pdf->SetLineWidth(0.5);
			    $pdf->Line(60,$yl1,60,$y1);
		      	$pdf->ln(1);
		      	$pdf->SetX(65);
		      	$pdf->MultiCell(0, 4, "Score:".$user_x_score, 0,'L');
		      	$y1=$pdf->GetY()+2;
			    $pdf->SetLineWidth(0.5);
			    $pdf->Line(60,$yl1,60,$y1);
		      	$pdf->ln(3);
			}
			$pdf->ln(3);
			//Work experience
			if($this->My_model->data_exists('user_id', $this->session->userdata('user_id'), 'user_employment_details'))
			{
				$user_work_ex=$this->My_model->load_rows('user_id', $this->session->userdata('user_id'), 'user_employment_details');
				$count=1;
				$pdf->SetFont('Arial','B',15);
			    $pdf->SetTextColor(45, 98, 183);
			    $y1=$pdf->GetY()+2;
			    $pdf->MultiCell(65,10, "Work Experience", 0,'L');
			    $pdf->SetLineWidth(0.5);
			    $pdf->Line(60,$yl1,60,$y1);
			    $pdf->SetY($y1);
			    $pdf->ln(5);
			    
				foreach($user_work_ex as $row)
					{
				      	$pdf->SetFont('Arial','B',13);
				      	$pdf->SetTextColor(0, 0, 0);
						$pdf->SetX(65);
				      	$pdf->MultiCell(0, 4, $row->user_company, 0,'L');
				      	$y1=$pdf->GetY()+2;
					    $pdf->SetLineWidth(0.5);
					    $pdf->Line(60,$yl1,60,$y1);
				      	$pdf->ln(1);
				      	$pdf->SetX(65);
				      	$pdf->SetFont('RobotoSlab','B',11);
				      	$pdf->MultiCell(0, 4, "Designation:".$row->user_job_profile, 0,'L');
				      	$y1=$pdf->GetY()+2;
					    $pdf->SetLineWidth(0.5);
					    $pdf->Line(60,$yl1,60,$y1);
				      	$pdf->SetX(65);
				      	$pdf->MultiCell(0, 4, "Duration: 2 years 4 months", 0,'L');
				      	$y1=$pdf->GetY()+2;
					    $pdf->SetLineWidth(0.5);
					    $pdf->Line(60,$yl1,60,$y1);
				      	$pdf->SetX(65);
				      	$pdf->MultiCell(0, 4, "Description:".$row->user_job_desc, 0,'L');
				      	$y1=$pdf->GetY()+2;
					    $pdf->SetLineWidth(0.5);
					    $pdf->Line(60,$yl1,60,$y1);
				      	$pdf->ln(3);


						$count++;	
					}
			}
			//Certification and training
			if($this->My_model->data_exists('user_id', $this->session->userdata('user_id'), 'user_training_details'))
			{
				$pdf->SetFont('Arial','B',15);
			    $pdf->SetTextColor(45, 98, 183);
			    $y1=$pdf->GetY()+2;
			    $pdf->MultiCell(40,10, "Certificate And Training", 0,'L');
			    $pdf->SetLineWidth(0.5);
			    $pdf->Line(60,$yl1,60,$y1);
			    $pdf->SetY($y1);
			    $pdf->ln(5);
				
				$count=1;

				$user_certificates=$this->My_model->load_rows('user_id', $this->session->userdata('user_id'), 'user_training_details');
				foreach($user_certificates as $row)
				{
				    $pdf->SetFont('Arial','B',13);
				    $pdf->SetTextColor(0, 0, 0);
					$pdf->SetX(65);
				    $pdf->MultiCell(0, 4, $row->user_training_course, 0,'L');
				    $y1=$pdf->GetY()+2;
				    $pdf->SetLineWidth(0.5);
				    $pdf->Line(60,$yl1,60,$y1);
				    $pdf->ln(1);
				    $pdf->SetX(65);
				    $pdf->SetFont('RobotoSlab','B',11);
			      	$pdf->MultiCell(0, 4, "Duration:".$row->user_training_duration." Months", 0,'L');
			      	$y1=$pdf->GetY()+2;
				    $pdf->SetLineWidth(0.5);
				    $pdf->Line(60,$yl1,60,$y1);
			      	$pdf->SetX(65);
			      	$pdf->MultiCell(0, 4, "Certified By:".$row->user_training_details, 0,'L');
			      	$y1=$pdf->GetY()+2;
				    $pdf->SetLineWidth(0.5);
				    $pdf->Line(60,$yl1,60,$y1);
			      	$pdf->ln(3);
					
					$count++;

				}
			}
			$pdf->ln(3);
			//Accolades
			if($this->My_model->data_exists('user_id', $this->session->userdata('user_id'), 'user_accolades'))
			{
				$pdf->SetFont('Arial','B',15);
			    $pdf->SetTextColor(45, 98, 183);
			    $y1=$pdf->GetY()+2;
			    $pdf->MultiCell(65,10, "Accolades And Acheivments", 0,'L');
			    $y1=$pdf->GetY()+2;
			    $pdf->SetLineWidth(0.5);
			    $pdf->Line(60,$yl1,60,$y1);
			    $pdf->SetY($y1);
			    $pdf->ln(5);
				$count=1;

				$user_accolades=$this->My_model->load_rows('user_id', $this->session->userdata('user_id'), 'user_accolades');

				foreach($user_accolades as $row)
				{
					$pdf->SetFont('Arial','B',13);
				    $pdf->SetTextColor(0, 0, 0);
					$pdf->SetX(65);
				    $pdf->MultiCell(0, 4, $row->accolade_title, 0,'L');
				    $y1=$pdf->GetY()+2;
				    $pdf->SetLineWidth(0.5);
				    $pdf->Line(60,$yl1,60,$y1);
				    $pdf->SetX(65);
				    $pdf->ln(1);
				    $pdf->SetFont('RobotoSlab','',11);
			      	$pdf->MultiCell(0, 4, "Details:".$row->accolade_details, 0,'L');
			      	$y1=$pdf->GetY()+2;
				    $pdf->SetLineWidth(0.5);
				    $pdf->Line(60,$yl1,60,$y1);
					$count++;

				}
			}
			$pdf->ln(3);
			//Skills
			if($this->My_model->data_exists('user_id', $this->session->userdata('user_id'), 'user_skills'))
			{

				$pdf->SetFont('Arial','B',15);
			    $pdf->SetTextColor(45, 98, 183);
			    $y1=$pdf->GetY()+2;
			    $pdf->MultiCell(65,10, "Skills", 0,'L');
			    $pdf->SetLineWidth(0.5);
			    $pdf->Line(60,$yl1,60,$y1);
			    $pdf->SetY($y1);
			    $pdf->ln(5);
				$count=1;

				$user_skill=$user_skill=$this->My_model->load_rows_join_condition('user_skills', 'skills', 'skill_id', 'skill_id', 'user_id', $this->session->userdata('user_id'));;
				foreach($user_skill as $row)
				{
					$pdf->SetX(65);
					$pdf->SetFont('Arial','B',13);
					$pdf->SetTextColor(0, 0, 0);
					$pdf->MultiCell(0, 4, $row->skill_name, 0,'L');
				    $y1=$pdf->GetY()+2;
				    $pdf->SetLineWidth(0.5);
				    $pdf->Line(60,$yl1,60,$y1);
			 
				}
			}	
			$pdf->ln(3);
			//Projects
			if($this->My_model->data_exists('user_id', $this->session->userdata('user_id'), 'user_projects'))
			{
				$pdf->SetFont('Arial','B',15);
			    $pdf->SetTextColor(45, 98, 183);
			    $y1=$pdf->GetY()+2;
			    $pdf->MultiCell(65,10, "Projects", 0,'L');
			    $pdf->SetLineWidth(0.5);
			    $pdf->Line(60,$yl1,60,$y1);
			    $pdf->SetY($y1);
			    $pdf->ln(5);
				$count=1;

				$user_projects=$this->My_model->load_rows('user_id', $this->session->userdata('user_id'), 'user_projects');
				foreach($user_projects as $row)
				{
					$pdf->SetFont('Arial','B',13);
				    $pdf->SetTextColor(0, 0, 0);
					$pdf->SetX(65);
				    $pdf->MultiCell(0, 4, $row->user_project_name, 0,'L');
				    $y1=$pdf->GetY()+10;
				    $pdf->SetLineWidth(0.5);
				    $pdf->Line(60,$yl1,60,$y1);
				    $pdf->ln(1);
				    $pdf->SetFont('RobotoSlab','B',11);
					if($row->user_project_link!='')
					{
						$pdf->SetX(65);
				    	$pdf->MultiCell(0, 4, 'Link:'.$row->user_project_link, 0,'L');
				    	$y1=$pdf->GetY()+10;
					    $pdf->SetLineWidth(0.5);
					    $pdf->Line(60,$yl1,60,$y1);
					}
					$pdf->SetX(65);
				    $pdf->MultiCell(0, 4, 'Details:'.$row->user_project_details, 0,'L');
					$count++;
					$pdf->ln(3);		

				}
			}
			$pdf->ln(3);
			$y1=$pdf->GetY()+2;
		    $pdf->SetLineWidth(0.5);
		    $pdf->Line(60,$yl1,60,$y1);
		    $pdf -> output ();
		}
		else
		{
			redirect('error');
		}     
	}
	
	public function resume3()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			define('FPDF_FONTPATH',APPPATH .'plugins/font/');
			require(APPPATH .'plugins/fpdf.php');


			// Instanciation of class
			$pdf = new FPDF();
			$pdf->SetMargins(2,2,2);
			$pdf->AddPage();
			//$pdf->SetAutoPageBreak(true,0);


			//Name of the student
			$this->load->Model('My_model');
			$user_details=$this->My_model->get_details('user_id', $this->session->userdata('user_id'), 'user');//to load personal info
			$fname=$user_details->fname;
			$lname=$user_details->lname;
			$email=$user_details->email;

			$user_additional_details=$this->My_model->get_details('user_id', $this->session->userdata('user_id'), 'user_additional_details');//to load personal info
			$phone=$user_additional_details->user_phone;
			$hometown=$user_additional_details->user_city;
			$cover_letter=$user_additional_details->user_cover_letter;

			//get college details
			$user_graduation_details=$this->My_model->get_details('user_id', $this->session->userdata('user_id'), 'user_graduation_details');
			$passing_year=$user_graduation_details->user_passing_year;
			$course=$user_graduation_details->user_course;
			$branch=$user_graduation_details->user_branch;
			$college=$user_graduation_details->user_college;
			$cgpa=$user_graduation_details->user_college;

			//header
			$pdf->SetFillColor(51,61,82);
			$pdf->SetTextColor(255);
			$pdf->SetFont('Times','BI',25);
			$pdf->Cell(0,32,'',0,0,'',true);
			$pdf->ln(1);
			$pdf->SetXY(5,5);
			$pdf->Cell(0,10,$fname." ".$lname,0,1);
			$pdf->SetFont('Arial','B',12);
			$pdf->SetX(5);
			$pdf->Cell(0,10,$course,0,1);
			$pdf->SetX(5);
			$pdf->Cell(0,0,$branch,0,1);
			        
			$pdf->ln(4);
			$pdf->SetFillColor(244,244,244);
			$pdf->SetTextColor(0);
			$pdf->Cell(0,52,'',0,0,'',true);
			$pdf->ln(10);
			$pdf->Cell(15,0,'Phone:');
			$pdf->SetFont('Arial','',12);
			$pdf->Cell(0,0,$phone);

			$pdf->SetX(100);
			$pdf->SetFont('Arial','B',12);
			$pdf->Cell(18,0,'College:');
			$pdf->SetFont('Arial','',12);
			$pdf->Cell(0,0,$college);

			$pdf->Ln(7);
			$pdf->SetFont('Arial','B',12);
			$pdf->Cell(15,0,'E-mail:');
			$pdf->SetFont('Arial','',12);
			$pdf->Cell(0,0,$email);

			$pdf->SetX(100);
			$pdf->SetFont('Arial','B',12);
			$pdf->Cell(24,0,'Hometown:');
			$pdf->SetFont('Arial','',12);
			$pdf->Cell(0,0,$hometown);

			$pdf->ln(9);
			$pdf->MultiCell(200,5,$cover_letter,'J');


			//Academic details

			if($this->My_model->data_exists('user_id', $this->session->userdata('user_id'), 'user_school_details'))
			{
				//set flag
				$data['school_info_exists']=1;
				//if yes send cgpa
				$user_school_info=$this->My_model->get_details('user_id', $this->session->userdata('user_id'), 'user_school_details');
				$user_xii_school=$user_school_info->user_xii_school_name;
				$user_xii_board=$user_school_info->user_xii_board;
				$user_xii_score=$user_school_info->user_xii_marks;
				$user_xii_year=$user_school_info->user_xii_passing_year;
				$user_x_school=$user_school_info->user_x_school_name;
				$user_x_board=$user_school_info->user_x_board;
				$user_x_score=$user_school_info->user_x_marks;
				$user_x_year=$user_school_info->user_x_passing_year;

				
				$pdf->ln(7);
				$pdf->SetFont('Times','BI',17);
				$pdf->Cell(0,0,"Academic Details");
				$pdf->ln(4);
				$pdf->SetDrawColor(180,180,180);
				$pdf->Cell(0,0,"",'B',1);
				$pdf->ln(5);
				
				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(0,0,"Passing Year: ".$user_xii_year);
				$pdf->SetX(40);
				$pdf->Cell(11,0,'Class XII- '.$user_xii_board);
				$pdf->SetFont('Arial','',10);
				$pdf->SetX(160);
				$pdf->Cell(5,0,"Percentage: ".$user_xii_score);
				$pdf->ln(4);
				$pdf->SetX(40);
				$pdf->MultiCell(165,5,$user_xii_school,'J');
				$pdf->ln(8);

				$pdf->SetFont('Arial','B',10);
				$pdf->Cell(0,0,"Passing Year: ".$user_x_year);
				$pdf->SetX(40);
				$pdf->Cell(11,0,'Class X- '.$user_x_board);
				$pdf->SetFont('Arial','',10);
				$pdf->SetX(160);
				$pdf->Cell(5,0,"Percentage: ".$user_x_score);
				$pdf->ln(4);
				$pdf->SetX(40);
				$pdf->MultiCell(165,5,$user_x_school,'J');

			}

			//experience

			if($this->My_model->data_exists('user_id', $this->session->userdata('user_id'), 'user_employment_details'))
			{
				$user_work_ex=$this->My_model->load_rows('user_id', $this->session->userdata('user_id'), 'user_employment_details');
				//Work Experience
				$count=1;
				$pdf->ln(10);
				$pdf->SetFont('Times','BI',17);
				$pdf->Cell(0,0,"Experience");
				$pdf->ln(4);
				$pdf->SetDrawColor(180,180,180);
				$pdf->Cell(0,0,"",'B',1);
				$pdf->ln(5);
				foreach($user_work_ex as $row)
				{
				 	$pdf->SetFont('Arial','B',10);
					$pdf->Cell(0,0,$row->user_job_start_date." to ");
					$pdf->SetX(40);
					$pdf->SetFont('Arial','B',13);
					$pdf->Cell(0,0,$row->user_job_profile);
					$pdf->ln(6);
					$pdf->SetFont('Arial','B',10);
					$pdf->Cell(0,0,$row->user_job_end_date);
					$pdf->SetX(40);
					$pdf->SetFont('Arial','I',10);
					$pdf->Cell(0,0,$row->user_company);
					$pdf->ln(5);
					$pdf->SetX(40);
					$pdf->SetFont('Arial','',10);
					$pdf->MultiCell(165,5,$row->user_job_desc,'J');
					$pdf->ln(8);

				 	$count++;


				}

			}

			
			//skills

			if($this->My_model->data_exists('user_id', $this->session->userdata('user_id'), 'user_skills'))
			{
				$pdf->SetFont('Times','BI',17);
				$pdf->Cell(0,0,"Skills");
				$pdf->ln(4);
				$pdf->SetDrawColor(180,180,180);
				$pdf->Cell(0,0,"",'B',1);
				$pdf->ln(5);

				$count=1;
				$temp=0;
				$pdf->SetFont('Arial','B',10);
			
				$user_skill=$user_skill=$this->My_model->load_rows_join_condition('user_skills', 'skills', 'skill_id', 'skill_id', 'user_id', $this->session->userdata('user_id'));;
				foreach($user_skill as $row)
				{	
					$temp++;
					if($temp==1)
					{
						$pdf->SetX(40);
					}
					elseif ($temp==2)
					{
						$pdf->SetX(100);
					}
					else
					{
						$pdf->SetX(150);
					}
					$pdf->MultiCellBlt(57,0,chr(149),$row->skill_name,0);
					//$pdf->Cell(57,0,$row->skill_name);
					if($temp==3)
        			{
        				$pdf->ln(6);
        				$temp=0;
        			}
					$count++;
				}
			}
			

			//projects

			if($this->My_model->data_exists('user_id', $this->session->userdata('user_id'), 'user_projects'))
			{
				$pdf->ln(4);
				$pdf->SetFont('Times','BI',17);
				$pdf->Cell(0,0,"Projects");
				$pdf->ln(4);
				$pdf->SetDrawColor(180,180,180);
				$pdf->Cell(0,0,"",'B',1);
				$pdf->ln(5);

				$count=1;

				$user_projects=$this->My_model->load_rows('user_id', $this->session->userdata('user_id'), 'user_projects');
				foreach($user_projects as $row)
				{
					$pdf->SetX(40);
					$pdf->SetFont('Arial','B',10);
					$pdf->Cell(57,0,$row->user_project_name);
					$pdf->SetX(160);
					$pdf->SetFont('Arial','',10);
					$pdf->Cell(0,0,"Link:");
					$pdf->ln(4);
					$pdf->SetX(160);
					$pdf->Cell(0,0,$row->user_project_link);
					$pdf->ln(8);

					$count++;

				}
			}


			//Certification and training
			if($this->My_model->data_exists('user_id', $this->session->userdata('user_id'), 'user_training_details'))
			{
				$pdf->SetFont('Times','BI',17);
				$pdf->Cell(0,0,"Certifications");
				$pdf->ln(4);
				$pdf->SetDrawColor(180,180,180);
				$pdf->Cell(0,0,"",'B',1);
				$pdf->ln(5);

				$count=1;

				$user_certificates=$this->My_model->load_rows('user_id', $this->session->userdata('user_id'), 'user_training_details');
				foreach($user_certificates as $row)
				{
					$pdf->SetFont('Arial','B',10);
					$pdf->Cell(0,0,$row->user_training_duration." Months");
					$pdf->SetX(40);
					$pdf->Cell(11,0,$row->user_training_course);
					$pdf->SetFont('Arial','',10);
					$pdf->SetX(160);
					$pdf->Cell(5,0,'Certified By: '.$row->user_training_company);
					$pdf->ln(4);
					$pdf->SetX(40);
					$pdf->MultiCell(165,5,$row->user_training_details,'J');
					$pdf->ln(8);

					$count++;

				}

			}


		$pdf->Output();

		}
		else
		{
			redirect('error');
		}     
	}
	public function resume4()
	{
		if($this->session->userdata('is_user_logged_in'))
		{
			define('FPDF_FONTPATH',APPPATH .'plugins/font/');
			require(APPPATH .'plugins/fpdf.php');
			 
					    
			$pdf = new FPDF('P','mm','A4');
			$pdf->SetMargins(0,0,0);
			$pdf->AddPage();
			$pdf->SetAutoPageBreak(true,0);

			$this->load->Model('My_model');
			$user_details=$this->My_model->get_details('user_id', $this->session->userdata('user_id'), 'user');//to load personal info
			$fname=$user_details->fname;
			$lname=$user_details->lname;
			$email=$user_details->email;

			$user_additional_details=$this->My_model->get_details('user_id', $this->session->userdata('user_id'), 'user_additional_details');//to load personal info
			$phone=$user_additional_details->user_phone;
			$hometown=$user_additional_details->user_city;
			$cover_letter=$user_additional_details->user_cover_letter;

			//get school details
			$user_school_info=$this->My_model->get_details('user_id', $this->session->userdata('user_id'), 'user_school_details');
			$user_xii_school=$user_school_info->user_xii_school_name;
			$user_xii_board=$user_school_info->user_xii_board;
			$user_xii_score=$user_school_info->user_xii_marks;
			$user_xii_year=$user_school_info->user_xii_passing_year;
			$user_x_school=$user_school_info->user_x_school_name;
			$user_x_board=$user_school_info->user_x_board;
			$user_x_score=$user_school_info->user_x_marks;
			$user_x_year=$user_school_info->user_x_passing_year;

			//get college details
			$user_graduation_details=$this->My_model->get_details('user_id', $this->session->userdata('user_id'), 'user_graduation_details');
			$passing_year=$user_graduation_details->user_passing_year;
			$course=$user_graduation_details->user_course;
			$branch=$user_graduation_details->user_branch;
			$college=$user_graduation_details->user_college;
			$cgpa=$user_graduation_details->user_college;

			$pdf->SetFillColor(55,61,72);
			$pdf->SetTextColor(255,255,255);
			$pdf->Cell(0,33,'',0,0,'',true);
			//Name
			$pdf->Ln(7);
			$pdf->SetFont('Arial','',28);
			$pdf->Cell(5,10,'',0,0);
			$pdf->Cell(0,10,$fname." ".$lname,0,1);
			//Designation
			$pdf->Ln(3);
			$pdf->SetFont('Arial','',13);
			$pdf->Cell(5,5,'',0,0);
			$pdf->Cell(0,5,$course,0,1);
			$pdf->Cell(5,5,'',0,0);
			$pdf->Cell(0,5,$branch,0,1);
			
			//------------------------------------------------------------------------------------------------------------------------
			//Description

			$pdf->SetTextColor(0,0,0);
			$pdf->Ln(10);
			$pdf->Cell(4,0);
			$pdf->setfont('Arial','B',10);
			$pdf->MultiCell(145,5,$cover_letter,0,1);

			//------------------------------------------------------------------------------------------------------------------------
			//Work experience

			if($this->My_model->data_exists('user_id', $this->session->userdata('user_id'), 'user_employment_details'))
			{
				$user_work_ex=$this->My_model->load_rows('user_id', $this->session->userdata('user_id'), 'user_employment_details');
				$count=1;
				
				$pdf->Ln(7);
				$pdf->Cell(4,0);
				$pdf->setfont('Arial','',15);
				$pdf->Cell(140,7,'Experience','B',1);
				foreach($user_work_ex as $row)
				{
				  $pdf->SetTextColor(0, 0, 0);
				  $pdf->SetFillColor(255, 255, 255);
				  $pdf->SetFont('Arial','',9);
			 	  $pdf->Ln(4);
			 	  $pdf->Cell(4,0);
				  $pdf->MultiCell(20,5,$row->user_job_start_date);
				  $pdf->SetXY($pdf->GetX()+25,$pdf->GetY()-5);
				  $pdf->SetFont('Arial','B',12);
				  $pdf -> cell(128,5,$count.'. '.$row->user_job_profile,0,1);
				  $pdf->Ln(2);
				  $pdf->Cell(25,0);
				  $pdf -> setFont ('Arial','I',12);
				  $pdf -> cell(128,5,$row->user_company,0,1);
				  $pdf->Cell(27,0);
				  $pdf -> setFont ('Arial','',11);
				  $pdf ->MultiCell(128,5,$row->user_job_desc,0,1);
				  $count++;
				}

			}


			//------------------------------------------------------------------------------------------------------------------------
			//College Education
				
			if($this->My_model->data_exists('user_id', $this->session->userdata('user_id'), 'user_graduation_details'))
			{
				$user_grad=$this->My_model->load_rows('user_id', $this->session->userdata('user_id'), 'user_graduation_details');
				$count=1;
				
				$pdf->Ln(7);
				$pdf->Cell(4,0);
				$pdf->setfont('Arial','',15);
				$pdf->Cell(140,7,'College education','B',1);
				foreach($user_grad as $row)
				{
				  $pdf->SetTextColor(0, 0, 0);
				  $pdf->SetFillColor(255, 255, 255);
				  $pdf->SetFont('Arial','',9);
			 	  $pdf->Ln(4);
			 	  $pdf->Cell(4,0);
				  $pdf->MultiCell(20,5,$row->user_passing_year);
				  $pdf->SetXY($pdf->GetX()+25,$pdf->GetY()-5);
				  $pdf->SetFont('Arial','B',12);
				  $pdf -> cell(128,5,$count.'. '.$row->user_course,0,1);
				  $pdf->Ln(2);
				  $pdf->Cell(27,0);
				  $pdf -> setFont ('Arial','',12);
				  $pdf ->MultiCell(128,5,"College:  ".$row->user_college,0,1);
				  $pdf->Cell(27,0);
				  $pdf -> cell(128,5,"Dept:      ".$row->user_branch,0,1);
				  
				  $count++;
				}

			}

			//-----------------------------------------------------------------------------------------------------------------
			//School Education
				
			if($this->My_model->data_exists('user_id', $this->session->userdata('user_id'), 'user_school_details'))
			{
				
				$pdf->Ln(7);
				$pdf->Cell(4,0);
				$pdf->setfont('Arial','',15);
				$pdf->Cell(140,7,'School education','B',1);
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFillColor(255, 255, 255);
				$pdf->SetFont('Arial','',9);
			 	$pdf->Ln(4);
			 	$pdf->Cell(4,0);
				$pdf->MultiCell(20,5,$user_xii_year);
				$pdf->SetXY($pdf->GetX()+25,$pdf->GetY()-5);
				$pdf->SetFont('Arial','B',12);
				$pdf -> cell(128,5,'Class XII- '.$user_xii_board,0,1);
				$pdf->Ln(2);
				$pdf->Cell(27,0);
				$pdf -> setFont ('Arial','',12);
				$pdf ->MultiCell(128,5,"School:   ".$user_xii_school,0,1);
				$pdf->Cell(27,0);
				$pdf -> cell(128,5,"Score:     ".$user_xii_score,0,1);

				$pdf->SetFont('Arial','',9);
			 	$pdf->Ln(4);
			 	$pdf->Cell(4,0);
				$pdf->MultiCell(20,5,$user_x_year);
				$pdf->SetXY($pdf->GetX()+25,$pdf->GetY()-5);
				$pdf->SetFont('Arial','B',12);
				$pdf -> cell(128,5,'Class X- '.$user_x_board,0,1);
				$pdf->Ln(2);
				$pdf->Cell(27,0);
				$pdf -> setFont ('Arial','',12);
				$pdf ->MultiCell(128,5,"School:   ".$user_x_school,0,1);
				$pdf->Cell(27,0);
				$pdf -> cell(128,5,"Score:     ".$user_x_score,0,1);
			}
			//------------------------------------------------------------------------------------------------------------
			//Certification and training
			if($this->My_model->data_exists('user_id', $this->session->userdata('user_id'), 'user_training_details'))
			{
				$pdf->Ln(7);
				$pdf->Cell(4,0);
				$pdf->setfont('Arial','',15);
				$pdf->Cell(140,7,'Certifications and trainings','B',1);
				$pdf->SetTextColor(0, 0, 0);
				$pdf->SetFillColor(255, 255, 255);
				$pdf->SetFont('Arial','',9);
				
				$count=1;

				$user_certificates=$this->My_model->load_rows('user_id', $this->session->userdata('user_id'), 'user_training_details');
				foreach($user_certificates as $row)
				{
				 	$pdf->Ln(4);
				 	$pdf->Cell(4,0);
					$pdf->MultiCell(20,5,$row->user_training_duration." Months");
					$pdf->SetXY($pdf->GetX()+25,$pdf->GetY()-5);
					$pdf->SetFont('Arial','B',12);
					$pdf -> cell(128,5,$count.'. '.$row->user_training_course,0,1);
					$pdf->Ln(2);
					$pdf->Cell(25,0);
					$pdf -> setFont ('Arial','I',12);
					$pdf ->MultiCell(128,5,'Certified By:   '.$row->user_training_company,0,1);
					$pdf -> setFont ('Arial','',11);
					$pdf->Cell(27,0);
					$pdf -> cell(128,7,$row->user_training_details,0,1);

					$count++;

				}
			}

			//------------------------------------------------------------------------------------------------------------------------
			//sidepane

			//personal info.
			$pdf->SetFillColor(244,244,244);
			$pdf->SetXY(152,33.3);
			$pdf->Cell(70,263,'',0,1,'',true);//background color
			$pdf->SetFont('Arial','B',14);
			$pdf->SetXY(156,37);
			$pdf->Cell(50,7,'Personal Info.','B',1);
			$pdf->SetFont('Arial','',12);
			$pdf->Ln(5);
			$pdf->SetX(158);
			$pdf->Cell(70,7,'Hometown',0,1,'',true);
			$pdf->SetFont('Arial','',10);
			$pdf->SetX(160);
			$pdf->MultiCell(50,5,$hometown,0,1);
			$pdf->SetFont('Arial','',12);
			$pdf->Ln(4);
			$pdf->SetX(158);
			$pdf->Cell(70,7,'Phone',0,1,'',true);
			$pdf->SetFont('Arial','B',10);
			$pdf->SetX(160);
			$pdf->Cell(50,5,$phone,0,1,'',true);
			$pdf->SetFont('Arial','',12);
			$pdf->Ln(4);
			$pdf->SetX(158);
			$pdf->Cell(70,7,'Email',0,1,'',true);
			$pdf->SetFont('Arial','B',10);
			$pdf->SetX(160);
			$pdf->Cell(50,5,$email,0,1,'',true);
			$pdf->SetFont('Arial','',12);
			$pdf->Ln(7);
			$pdf->SetX(155);

			//Skills
			if($this->My_model->data_exists('user_id', $this->session->userdata('user_id'), 'user_skills'))
			{
				
				$count=1;
				$pdf->SetFont('Arial','B',14);
				$pdf->SetX(156);
				$pdf->Cell(50,7,'Skills','B',1);
				$pdf->Ln(4);
				$pdf->SetFont('Arial','',11);
				
				$user_skill=$user_skill=$this->My_model->load_rows_join_condition('user_skills', 'skills', 'skill_id', 'skill_id', 'user_id', $this->session->userdata('user_id'));;
				foreach($user_skill as $row)
				{
					$pdf->SetX(158);
					$pdf->Cell(70,7,$row->skill_name,0,1,'',true);
				}
			}

			//Projects
			if($this->My_model->data_exists('user_id', $this->session->userdata('user_id'), 'user_projects'))
			{
				$pdf->SetFont('Arial','B',14);
				$pdf->Ln(7);
				$pdf->SetX(157);
				$pdf->Cell(50,7,'Projects','B',1);
				$pdf->Ln(4);
				
				$count=1;

				$user_projects=$this->My_model->load_rows('user_id', $this->session->userdata('user_id'), 'user_projects');
				foreach($user_projects as $row)
				{
					$pdf->SetTextColor(0,0,0);
					$pdf->SetFont('Arial','',11);
					$pdf->SetX(158);
					$pdf->Cell(70,5,$row->user_project_name,0,1);
					$pdf->SetX(155);
					$pdf->SetFont('Arial','',10);
					$pdf->SetTextColor(0,0,255);
					$pdf->Cell(50,5,$row->user_project_link,0,1,'R');
					$pdf->Ln(3);					
					$count++;

				}
			}

			//Acheivements
			if($this->My_model->data_exists('user_id', $this->session->userdata('user_id'), 'user_accolades'))
			{
				
				$count=1;
				$pdf->Ln(4);
				$pdf->SetTextColor(0,0,0);
				$pdf->SetFont('Arial','B',14);
				$pdf->SetX(156);
				$pdf->Cell(50,7,'Acheivements','B',1);
				$pdf->Ln(4);
				$pdf->SetFont('Arial','',11);
				
				$user_accolades=$this->My_model->load_rows('user_id', $this->session->userdata('user_id'), 'user_accolades');
				foreach($user_accolades as $row)
				{
					$pdf->SetX(158);
					$pdf->Cell(70,7,$row->accolade_title,0,1,'',true);
				}
			}

			  $pdf -> output ();
		}
		else
		{
			redirect('error');
		}     
	}

	public function change_password()
    {
        if($this->session->userdata('is_user_logged_in') && isset($_POST['email']))
        {
            
            $url=base_url()."/profileapi/change_password";

			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

			$headers = array('X-Api-Key:59671596837f42d974c7e9dcf38d17e8');

			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

			$password_change = array(
			    'key1' => 'email',
			    'value1'=> $_POST['email'],
			    'key2' => 'password',
			    'value2'=> $_POST['old_password'],
			    'key3' => 'newPassword',
			    'value3'=> $_POST['new_password']
			);

			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($password_change));

			$response = curl_exec($ch);
			curl_close($ch);

			$password_response = json_decode($response, true);

			if($password_response['status'] == 200)
			{
				$this->session->set_flashdata('msg_password','Successfully changed your password!');
            	redirect($_POST['current_url']);
			}
			else
            {
                $this->session->set_flashdata('msg_password','Incorrect email/password!');
                redirect($_POST['current_url']);
            }
        }
    }


}

?>