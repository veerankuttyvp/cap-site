/* Controllers */
angular.module('cap.controllers', []).

controller('NavCtrl', ['$scope',
    function($scope) {
        $scope.showMenu = function(){
            $('.hover-menu-container').addClass('active');
            $('.hover-menu').removeClass('hide');
        }
        $scope.hideMenu = function(){
            $('.hover-menu-container').removeClass('active');
            $('.hover-menu').addClass('hide');
        }

    }
]).

controller('TestCtrl', ['$scope',
    function($scope) {}
]);


function validateEmail(email) {
    var re = /\S+@\S+\.\S+/;
    return re.test(email);
}

function save_new_note($scope, $http){
    console.log(validate_note($scope));
    if(validate_note($scope)){
        params = {
            'title': $scope.current_note.title,
            'created': 'dummy txt',
            'id': $scope.current_note.id,
            'share_with_mentee': $scope.current_note.share_with_mentee,
            'mentee' :$scope.mentee_id,
        }
        if($scope.edit_flag){
            var url = "/user/notes/"+$scope.current_note.id;
            $http({
            method: 'put',
                url: url,
                data: angular.toJson(params),
                headers: {
                    'Content-Type': 'application/json'
                }
            }).success(function(data, status) {
                if(data.status == "true"){
                    $scope.msg = "Note saved Successfully";
                } else {
                    $scope.msg = data.message;
                }
                $scope.get_notes();
                $scope.msg = '';
                $scope.edit_flag = false;
                $scope.show_popup = false;
                $scope.clear_current_note();
            }).error(function(data, success){
            });
        } else {
            var url = "/user/notes/"
            $http({
            method: 'post',
                url: url,
                data: angular.toJson(params),
                headers: {
                    'Content-Type': 'application/json'
                }
            }).success(function(data, status) {
                if(data.status == "true"){
                    $scope.msg = "Note saved Successfully";
                } else {
                    $scope.msg = data.message;
                }
                $scope.get_notes();
                $scope.msg = '';
                $scope.edit_flag = false;
                $scope.show_popup = false;
                $scope.clear_current_note();
            }).error(function(data, success){
            });
        }

    }
}
function delete_note(note, $scope, $http){

    $http.delete("/user/notes/"+note.id).success(function(data)
    {
        $scope.get_notes();
    }).error(function(data, status)
    {
         console.log(data || "Request failed");
    });
}
function edit_note(note, $scope){
    $scope.edit_flag = true;
    $scope.msg = false;
    $scope.show_popup = true;
    $scope.current_note.title = note.title;
    $scope.current_note.id = note.id;
    $scope.current_note.description = note.description;
}
function validate_note($scope){
    if($scope.current_note.title == ''){
        $scope.msg = "Please Enter Title";
        return false;
    } else {
        return true;
    }
    return true;
}
function clear_current_note($scope){
    $scope.current_note.title = '';
    $scope.current_note.id = '';
    $scope.current_note.share_with_mentee = false;
}
function LoginControllerOff($scope, $element, $http, $timeout, $location, $cookies)
{
}


function ForgotPasswordController($scope, $element, $http, $timeout, $location)
{
    $scope.init = function(token){
        $scope.email = '';
        $scope.old_password = '';
        $scope.new_password = '';
        $scope.token_value = token;
    }
    $scope.validate_form = function(){
        if($scope.email == ''){
            $scope.msg = "Please enter username";
            return false;
        } else if(!validateEmail($scope.email)){
            $scope.msg = "Please enter a valid email address";
            return false;
        } else {
            $scope.msg = '';
            return true;
        }
    }
    $scope.validate_password= function(){
        if($scope.old_password == ''){
            $scope.msg = "Please enter old password";
            return false;
        } else if($scope.new_password == ''){
            $scope.msg = "Please enter new password";
            return false;
        } else {
            $scope.msg = '';
            return true;
        }
    }
    $scope.forgot_password = function(){
        $scope.is_valid = $scope.validate_form();
        if ($scope.is_valid) {
            params = {
                'email': $scope.email,
            }
            $http({
                method: 'post',
                url: "/user/register",
                data: angular.toJson(params),
                headers: {
                    'Content-Type': 'application/json'
                }
            }).success(function(data, status) {
                if(data.status == "sent"){
                    $scope.msg = "Your request sent Successfully, Please check your email";
                } else {
                    $scope.msg = "Some error occured";
                }
            }).error(function(data, success){
            });
        }
    }

    $scope.change_password = function(){
        console.log($scope.token_value);
        if ($scope.new_password != '' && $scope.confirm_password != '') {
            params = {
                'new_password': $scope.new_password,
                'confirm_password': $scope.confirm_password,
                'token': $scope.token_value
            }
            $http({
                method: 'post',
                url: "/user/passwordreset",
                data: angular.toJson(params),
                headers: {
                    'Content-Type': 'application/json'
                }
            }).success(function(data, status) {
                if(data.passwordreset == "success"){
                    $scope.msg = "Password changed successfully";
                    document.location.href = "../../login";
                } else {
                    $scope.msg = "Some error occured";
                }
            }).error(function(data, success){

            });
        } else {
            $scope.msg = "Please Enter Old password and New Password";
        }
    }
}



{
}

function MentorMenteeController($scope, $element, $http, $timeout, $location, $cookies)
{
    $scope.init = function(mentor_id){
        $scope.mentor_id = mentor_id
        $scope.user_type = "Administrator";
        $scope.get_mentor_mentees();
    }
    $scope.show_menu = function(){
        $('#menu').css('display', 'block');
    }
    $scope.hide_menu = function(){
        $('#menu').css('display', 'none');
    }
    $scope.get_mentor_mentees = function(){
        $http.get("/user/adminmentor/"+$scope.mentor_id).success(function(data)
        {
            $scope.mentees = data.data;
        }).error(function(data, status)
        {
            console.log(data || "Request failed");
        });
    }

    $scope.delete_mentor_mentees = function(mentee){

        $http.delete("/user/adminmentor/"+mentee.id).success(function(data)

        {
            $scope.get_mentor_mentees();
        }).error(function(data, status)
        {
             console.log(data || "Request failed");
        });
    }
}

function QuestionairController($scope, $element, $http, $timeout, $location, $cookies)
{
    $scope.init = function(questionair_id){
        $scope.user_id = $cookies.user_id;
        $scope.role_id = $cookies.role_id;
        $scope.questionair_id = questionair_id;
        if($scope.role_id == 1){
            $scope.user_type = "Administrator";
        } else if($scope.role_id == 2){
            $scope.user_type = "Mentor";
        } else {
            $scope.user_type = "Mentee";
        }
        $scope.get_questions();
    }
    $scope.get_questions = function(){
        $http.get("/user/saqlist/"+$scope.questionair_id).success(function(data)
        {
            $scope.questions = data.data;
        }).error(function(data, status)
        {
            console.log(data || "Request failed");
        });
    }
    $scope.show_menu = function(){
        $('#menu').css('display', 'block');
    }
    $scope.hide_menu = function(){
        $('#menu').css('display', 'none');
    }

}

function SettingsController($scope, $element, $http, $timeout, $location, $cookies)
{
    $scope.show_menu = function(){
        $('#menu').css('display', 'block');
    }
    $scope.hide_menu = function(){
        $('#menu').css('display', 'none');
    }
}



function CreateAccountController($scope, $element, $http, $timeout, $location, $cookies)
{
    $scope.init = function(){
        $scope.user_id = $cookies.user_id;
        $scope.role_id = $cookies.role_id;
        $scope.account_type = '';
        $scope.name = '';
        $scope.title = '';
        $scope.phone_number = '';
        $scope.email = '';
        if($scope.role_id == 4){
            $scope.user_type = "Administrator";
        } else if($scope.role_id == 5){
            $scope.user_type = "Mentor";
        } else if($scope.role_id == 6){
            $scope.user_type = "Mentee";
        }
        $scope.msg = '';
    }
    $scope.validate_form = function(){
        if($scope.account_type == ''){
            $scope.msg = "Please Select Account Type";
            return false;
        } else if($scope.name == ''){
            $scope.msg = "Please Enter name";
            return false;
        } else if($scope.title == ''){
            $scope.msg = "Please enter Title";
            return false;
        } else if($scope.phone_number == ''){
            $scope.msg = "Please Enter phone number";
            return false;
        } else if(!validateEmail($scope.email)) {
            $scope.msg = "Please enter a valid email address";
            return false
        } else {
            return true;
        }
    }
    $scope.create_account = function(){
        if($scope.validate_form()){
            params = {
                'account_type': $scope.account_type,
                'name': $scope.name,
                'title': $scope.title,
                'phone_number': $scope.phone_number,
                'email': $scope.email,
            }
            $http({
                method: 'post',
                url: "/user/create",
                data: angular.toJson(params),
                headers: {
                    'Content-Type': 'application/json'
                }
            }).success(function(data, status) {
                if(data.status == "true"){
                    $scope.msg = "Account created Successfully";
                } else {
                    $scope.msg = data.message;
                }
            }).error(function(data, success){
            });
        }
    }
    $scope.show_menu = function(){
        $('#menu').css('display', 'block');
    }
    $scope.hide_menu = function(){
        $('#menu').css('display', 'none');
    }
}

function AdminMenteeController($scope, $element, $http, $timeout, $location, $cookies)
{
    $scope.init = function(user,role, mentee_id){
        $scope.user_id = user;
        $scope.role_id = role;
        $scope.mentee_id = mentee_id;
        console.log(mentee_id);
        $scope.show_popup = false;
        if($scope.role_id == 4){
            $scope.user_type = "Administrator";
        } else if($scope.role_id == 5){
            $scope.user_type = "Mentor";
        } else {
            $scope.user_type = "Mentee";
        }
        $scope.get_mentee_saq_list();
    }
    $scope.get_mentee_saq_list = function(){
        $http.get("/user/adminmentee/"+$scope.mentee_id).success(function(data)
        {
            $scope.mentee_saq_list = data.data;
        }).error(function(data, status)
        {
            console.log(data || "Request failed");
        });
    }
}


function AdminMenteeSAQDetailController($scope, $element, $http, $timeout, $location, $cookies)
{
    $scope.init = function(user,role, saq_id){
        $scope.user_id = user;
        $scope.role_id = role;
        $scope.saq_id = saq_id;
        $scope.show_popup = false;
        if($scope.role_id == 4){
            $scope.user_type = "Administrator";
        } else if($scope.role_id == 5){
            $scope.user_type = "Mentor";
        } else {
            $scope.user_type = "Mentee";
        }
        $scope.get_saq_details();
    }
    $scope.get_saq_details = function(){
        $http.get("/user/adminsaqresult/"+$scope.saq_id).success(function(data)
        {
            $scope.saq = data.data;
        }).error(function(data, status)
        {
            console.log(data || "Request failed");
        });
    }
}

function MenteeDetailController($scope, $element, $http, $timeout, $location, $cookies)
{
    $scope.init = function(user,role, mentee_id){
        $scope.user_id = user;
        $scope.role_id = role;
        $scope.show_popup = false;
        $scope.ques_page_interval = 5;
        $scope.notes_page_interval = 5;
        $scope.shared_page_interval = 5;
        $scope.mentee_id = mentee_id;
        $scope.edit_flag = false;
        if($scope.role_id == 4){
            $scope.user_type = "Administrator";
        } else if($scope.role_id == 5){
            $scope.user_type = "Mentor";
        } else {
            $scope.user_type = "Mentee";
        }
        $scope.get_mentee_saq_list();
        $scope.get_notes();
        $scope.get_mentee_shared_notes();
        $scope.current_note = {};
        $scope.clear_current_note();

    }
    $scope.range = function(n) {
        return new Array(n);
    }
    $scope.getClass = function(page) {
        if(page == $scope.current_page)
            return "current";
        else
            return '';
    }
    $scope.get_mentee_saq_list = function(){
        $http.get("/user/adminmentee/"+$scope.mentee_id).success(function(data)
        {
            $scope.mentee_saq_list = data.data;
        }).error(function(data, status)
        {
            console.log(data || "Request failed");
        });
    }

    $scope.get_mentee_shared_notes = function(){
        $http.get("/user/notes/"+$scope.mentee_id).success(function(data)
        {
            $scope.menteesharednotes = data.data;
        }).error(function(data, status)
        {
            console.log(data || "Request failed");
        });
    }

    $scope.show_menu = function(){
        $('#menu').css('display', 'block');
    }
    $scope.hide_menu = function(){
        $('#menu').css('display', 'none');
    }
    $scope.get_notes = function(){
        $http.get("/user/notes/").success(function(data)
        {
            $scope.menteenotes = data.data;
        }).error(function(data, status)
        {
            console.log(data || "Request failed");
        });
    }
    $scope.create_note = function(){
        $scope.show_popup = true;
    }
    $scope.edit_note = function(note){
        edit_note(note, $scope);
    }
    $scope.validate_note = function(){
        validate_note($scope);
    }
    $scope.clear_current_note = function(){
        clear_current_note($scope);
    }
    $scope.save_new_note = function(){
        save_new_note($scope, $http);
    }
    $scope.delete_note = function(note){

        delete_note(note, $scope, $http);
    }
}

function MenteeSAQInterface($scope, $element, $http, $timeout, $location, $cookies){
    $scope.init = function(questionare_id){
        $scope.saq_id = questionare_id;
        $scope.get_saq_details();
        $scope.continue = true;
        $scope.pause = false;
        $scope.saq = {
            'name': 'Learning to use force',
            'completion_percentage': '50%',
            'description': "Lorem Ipsum is simply dummy text of the printing and typesetting industry.\
                            Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,\
                             when an unknown printer took a galley of type and scrambled it to make a type \
                             specimen book. It has survived not only five centuries, but also the leap into \
                             electronic typesetting, remaining essentially unchanged. It was popularised in the \
                             1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more \
                             recently with desktop publishing software like Aldus PageMaker including versions \
                             of Lorem Ipsum.",
            'sections': [
                {
                    'name': 'Moving Objects',
                    'total_questions': '35',
                    'completed_questions': '35',
                    'status': 'Completed',
                },
                {
                    'name': 'Sensing Disturbances',
                    'total_questions': '22',
                    'completed_questions': '40',
                    'status': 'In Progress',
                },
                {
                    'name': 'Jedi Mind Tricks',
                    'total_questions': '0',
                    'completed_questions': '25',
                    'status': 'Not Started',
                },
            ]
        }
    }
    $scope.get_saq_details = function(){
        params = {
            'qid': $scope.saq_id,
        }
        $http({
            method: 'post',
            url: "/user/test",
            data: $.param(params),
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data, status) {
            if(data.status){
                $scope.message = "Test " + data.status;
                $scope.question.answer_type = data.status;
            } else {
                $scope.question = data.question[0];
                $scope.answers = data.answer;
                if(data.answer.length > 0){
                    $scope.question.answer_type = data.answer[0].answerType;
                } else {
                    $scope.question.answer_type = 'TEXT';
                }
                $scope.question.saq_name = $scope.question.saq_name;
            }
        }).error(function(data, success){
        });
    }
    $scope.save_answer = function(){
        params = {
            'id': $scope.question.id,
            'AnswerSubmit': 1,
        }
        if($scope.question.answer_type=='MULTISELECT'){
            params['selected_answers'] = angular.toJson($scope.question.selected_answers);
        }
        if($scope.question.answer_type=='ENUM'){
            params['selected_answers'] = angular.toJson($scope.answers);
            params['answers_type'] = 'ENUM';
        }
        if($scope.question.answer_type=='TEXT'){
            params['selected_answers'] = $scope.question.answer_text;
            params['answers_type'] = 'TEXT';
        }
        if($scope.question.answer_type=='TEXTAREA'){
            params['selected_answers'] = $scope.question.answer_textarea;
            params['answers_type'] = 'TEXTAREA';
        }
        if($scope.question.answer_type=='CHECKBOX'){
            params['selected_answers'] = angular.toJson($scope.answers);
            params['answers_type'] = 'CHECKBOX';
        }
        if($scope.question.answer_type=='RADIO'){
           params['selected_answers'] = $scope.question.radio_choice;
        }
        if($scope.question.answer_type=='SELECT'){
           params['selected_answers'] = $scope.question.answer_choice;
        }
        $http({
            method: 'post',
            url: "/user/test",
            data: $.param(params),
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data, status) {
            if(data.status){
                $scope.message = "Test " + data.status;
                $scope.question.answer_type = data.status;
            } else {
                $scope.question = data.question[0];
                $scope.answers = data.answer;
                if(data.answer.length > 0){
                    $scope.question.answer_type = data.answer[0].answerType;
                } else {
                    $scope.question.answer_type = 'TEXT';
                }
                $scope.question.saq_name = $scope.question.saq_name;
            }
        }).error(function(data, success){
        });
    }
    $scope.pause_saq = function(){
        $scope.continue = false;
        $scope.pause = true;
       // params = {
       //     'saq_id': $scope.saq_id,
      //  }
      //  $http({
      //      method: 'post',
      //      url: "/user/saqsummary",
     //       data: $.param(params),
     //       headers: {
//
 //               'Content-Type': 'application/x-www-form-urlencoded'
  //          }
   //     }).success(function(data, status) {
    //        //$scope.saq = data.saq;
     //   }).error(function(data, success){
      //  });

       $http.get("/user/summary/"+$scope.saq_id).success(function(data)
        {
            $scope.saq = data.data;
        }).error(function(data, status)
        {
            console.log(data || "Request failed");
        });

    }
    $scope.continue_test = function(){
        $scope.continue = true;
        $scope.pause = false;
    }
}

function SAQDetail($scope, $element, $http, $timeout, $location, $cookies){
    $scope.init = function(){

    }
    $scope.get_saq_details = function(){
        params = {
            'mentee_id': mentee.id,
            'questionnaire_id': $scope.selected_saq.id
        }
        $http({
            method: 'post',
            url: "/user/saqlist",
            data: $.param(params),
            headers: {

                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data, status) {
        }).error(function(data, success){
        });
    }
}
