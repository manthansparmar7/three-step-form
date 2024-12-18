jQuery(document).ready(function ($) {
    const skillsContainer = $('#skills-container');
    const selectedSkillsList = $('#selected-skills-list');
    const selectedSkillsLabel = $('#selected-skills-label');
    const searchInput = $('#search-skill');
    const maxSkills = 7; // Max number of skills allowed to select

    // Skills array for demonstration
    const allSkills = [
        "User Flows", "AWS EC2", "Node.js", "PHP", "JavaScript", "React.js", "Vue.js", "Python", "Java", "C#", 
        "Ruby", "Go", "Swift", "HTML", "CSS", "SQL", "MongoDB", "Docker", "Kubernetes", "MySQL", "PostgreSQL", 
        "Laravel", "Angular", "React Native", "Spring Boot", "Next.js", "TypeScript", "Git", "Jenkins", "Nginx",
        "Redis", "AWS Lambda", "Bootstrap", "TailwindCSS", "SASS", "GraphQL", "ElasticSearch", "WebSockets", 
        "OAuth2", "Firebase", "Django", "Flask", "Machine Learning", "AI", "PHPUnit", "Redux", "Jest", "Cypress"
    ];

    let selectedSkills = []; // Track selected skills globally

    // Function to render skills based on search query
    function renderSkills(query = "") {
        skillsContainer.empty();

        // Filter skills based on search query
        const filteredSkills = allSkills.filter(skill => skill.toLowerCase().includes(query.toLowerCase()));

        // Separate selected skills from the filtered list
        const selected = filteredSkills.filter(skill => selectedSkills.includes(skill));
        const unselected = filteredSkills.filter(skill => !selectedSkills.includes(skill));

        // Merge selected skills first, followed by unselected skills
        const sortedSkills = [...selected, ...unselected];

        // Render only the first 13 skills (show priority selected skills at the top)
        const skillsToRender = sortedSkills.slice(0, 13);

        // Render the skills
        skillsToRender.forEach(skill => {
            const isSelected = selectedSkills.includes(skill); // Check if the skill was selected before
            const skillTag = $('<span class="skill-tag">')
                .text(skill)
                .attr('data-skill', skill)
                .append(`<span class="icon">${isSelected ? 'x' : '+'}</span>`);

            // If the skill is selected, apply the selected state
            if (isSelected) {
                skillTag.addClass('selected');
            }

            // Add event listener for selection
            skillTag.on('click', function () {
                toggleSkill(skillTag); // Call toggleSkill when a tag is clicked
            });

            skillsContainer.append(skillTag);
        });
    }

    // Function to toggle skill selection
    function toggleSkill(skillElement) {
        const skillName = skillElement.data('skill');
        const icon = skillElement.find('.icon');

        if (skillElement.hasClass('selected')) {
            // Deselect skill
            skillElement.removeClass('selected');
            icon.text('+');
            selectedSkills = selectedSkills.filter(skill => skill !== skillName);
        } else {
            // Check if the selected skills count is already at max
            if (selectedSkills.length >= maxSkills) {
                // Show validation message when trying to select more than max allowed skills
                alert(`You can select up to ${maxSkills} skills only.`);
                return; // Prevent further selection
            }

            // Select skill
            skillElement.addClass('selected');
            icon.text('x');
            selectedSkills.push(skillName);
        }

        updateSelectedSkills(); // Update the selected skills list
    }

    // Function to update the selected skills list
    function updateSelectedSkills() {
        selectedSkillsList.empty();  // Clear the list

        // Update label based on the number of selected skills
        if (selectedSkills.length > 0) {
            selectedSkillsLabel.html(`<strong>${selectedSkills.length} skill${selectedSkills.length > 1 ? 's' : ''} selected</strong>`);
        } else {
            selectedSkillsLabel.text('');
        }

        // Update hidden input field with selected skills as a JSON string
        $('#selected-skills-input').val(JSON.stringify(selectedSkills));
    }

    // Event listener for skill search input
    searchInput.on('input', function () {
        renderSkills($(this).val()); // Re-render the skills with the search query
    });

    // Function to reset search and preserve the selected state
    searchInput.on('focusout', function () {
        renderSkills(searchInput.val()); // Re-render with selected skills state preserved
    });

    // Initial render (show first 13 skills)
    renderSkills();

    // Prevent form submission and handle step navigation for "Next" button
    $('.next-step').click(function (event) {
        event.preventDefault();  // Prevent form submission

        var nextStep = $(this).data('next');
        
        // Step 1: Set skills to hidden field (already done in updateSelectedSkills function)
        // Step 2: Set option selected to hidden field
        if (nextStep === 3) {
            var selectedOption = $('input[name="option"]:checked').val(); // Get selected option
            $('#selected-option-input').val(selectedOption);
        }

        // Move to the next step
        $('.step').hide();
        $('#step-' + nextStep).show();
    });

    // Prevent form submission and handle step navigation for "Previous" button
    $('.prev-step').click(function (event) {
        event.preventDefault();  // Prevent form submission

        var prevStep = $(this).data('prev');
        
        // Move to the previous step
        $('.step').hide();
        $('#step-' + prevStep).show();
    });

    // AJAX form submission on final step
    $('#submit-form').click(function (event) {
        event.preventDefault();  // Prevent default form submission

    // Gather all form data
    var formData = {
        selected_skills: JSON.stringify(selectedSkills), // Pass the selected skills as a JSON string
        selected_option: $('#selected-option-input').val(), // Option selected in the form
        name: $('#user-name').val(), // Example: Name field
        email: $('#user-email').val(), // Example: Email field
        phone: $('#user-phone').val(), // Example: Phone field
        terms_agreed: $('#user-terms-checkbox').prop('checked'), // Example: Terms and Conditions checkbox
        // Add other fields as needed
    };

    // You can also loop through form fields dynamically if the form has many fields:
    $('#my-form').find('input, select, textarea').each(function () {
        var input = $(this);
        var inputName = input.attr('name'); // Get the field name
        if (inputName) {
            if (input.is('input[type="checkbox"]')) {
                formData[inputName] = input.prop('checked'); // For checkbox fields
            } else if (input.is('select') || input.is('input[type="text"]') || input.is('textarea')) {
                formData[inputName] = input.val(); // For text inputs and selects
            }
        }
    });

        $.ajax({
            url: stepFormAjax.ajax_url, // URL of admin-ajax.php
            method: 'POST',
            data: {
                action: 'step_form_handle_submission', // The action name you will use in WordPress
                form_data: formData, // The form data to send to the backend
            },
            beforeSend: function () {
                $('#submit-form').prop('disabled', true); // Disable the submit button during submission
            },
            success: function (response) {
                if (response.success) {
                    alert('Form submitted successfully!');
                    location.reload();
                    // Optionally, reset form or redirect user
                } else {
                    alert('There was an error submitting the form: ' + response.data);
                }
            },
            error: function () {
                alert('There was a problem with the form submission.');
            },
            complete: function () {
                $('#submit-form').prop('disabled', false); // Re-enable the submit button after submission
            }
        });
        
    });
});
