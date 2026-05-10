function initCalendar() {
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;
    if (calendarEl.dataset.initialized) return;
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },

        events: [
            {
                title: "Research Proposal",
                start: "2026-04-10"
            },
            {
                title: "Math Exam",
                start: "2026-04-18"
            }
        ]
    });

    calendar.render();
    calendarEl.dataset.initialized = "true";
}


/**
 * Highlight active sidebar link based on URL
 */
function highlightActiveMenu() {
    const links = document.querySelectorAll('.dashboard-sidebar a');
    const currentPath = window.location.pathname;
    links.forEach(link => {
        link.classList.remove('active');
        const href = link.getAttribute('href');
        if (href === currentPath) {
            link.classList.add('active');
        }
    });
}


/**
 * Initialize AJAX page navigation
 */
function initAjaxNavigation() {
    const content = document.querySelector('.dashboard-content');
    document.body.addEventListener('click', function(e) {
        const link = e.target.closest('.dashboard-sidebar a');
        if (!link) return;
        const url = link.getAttribute('href');
        if (!url || url.startsWith('http') || link.target === '_blank') return;
        e.preventDefault();
        loadPage(url);
    });
}


/*================================================================================
        Load page via AJAX
        The optional chaining ?.() help the script not to crash 
        if those functions don't exist yet.
==================================================================================*/
function loadPage(url) {
    const content = document.querySelector('.dashboard-content');

    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.text())
    .then(html => {
        content.innerHTML = html;

        history.pushState({}, '', url);
        highlightActiveMenu();
        initCalendar();
        //initCharts?.();
        //initTables?.();
        //initUploaders?.()
    })
    .catch(err => console.error(err));
}

function updateBadge(url, elementId) {

    fetch(url)
        .then(response => response.json())
        .then(data => {

            const badge = document.getElementById(elementId);
            if (!badge) return;

            const count = data.count ?? 0;
            badge.textContent = count;
            badge.style.display = count > 0
                ? 'inline-block'
                : 'none';
        })
        .catch(() => {});
}

function refreshBadges() {
    updateBadge('/messages/unread', 'messages_badge');
    updateBadge('/notifications/unread', 'notifications_badge');
    updateBadge('/announcements/unread', 'announcements_badge');
}

/* TOGGLE CREATE FORM */
function toggleForm(form, e) {
    const wrapper = document.querySelector('.create-'+form+'-wrapper');
    if(!wrapper) return false;
    wrapper.classList.toggle('open');
    if(wrapper.classList.contains('open')){
        e.target.textContent = "Close form";
        e.target.classList.add('btn-active');
    } 
    else {
        e.target.textContent = "Open form";
        e.target.classList.remove('btn-active');
        e.target.classList.add('toggle-'+form+'-btn');
    }
    return true;
}


function createEntity(type, url, fields) {
    fetch('/dashboard/courses/crud/' + type + 's', {
        method:'POST',
        headers:{
            'Content-Type':'application/x-www-form-urlencoded',
            'X-Requested-With':'XMLHttpRequest'
        },
        body: new URLSearchParams(fields).toString()
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            loadPage(url);
        } 
        else {
            alert(data.message || 'Something went wrong');
        }
    })
}

function titleClick(title) {
    const list = title.nextElementSibling;

    if (list.style.maxHeight) {
        list.style.maxHeight = null;
        title.classList.remove('open');
    } else {
        list.style.maxHeight = list.scrollHeight + "px";
        title.classList.add('open');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // initialize components for first load
    highlightActiveMenu();
    initAjaxNavigation();
    initCalendar();
    //initCharts?.();
    //initTables?.();
    //initUploaders?.();
});


/**
 * Sidebar hamburger toggle
 */
document.querySelector('.hamburger')?.addEventListener('click', () => {
    document.querySelector('.dashboard-sidebar')
        ?.classList.toggle('open');
});


/**
 * Handle browser back/forward buttons
 */
window.addEventListener('popstate', () => {
    loadPage(window.location.pathname);
});



// Run immediately
refreshBadges();

// Refresh every 10 seconds
setInterval(refreshBadges, 10000);


document.addEventListener('click', function(e){
    
    // Dashboard menu list toggle
    const menu_title = e.target.closest('.menu-title');
    if (menu_title) {
        titleClick(menu_title);
    }
    
    // Course, module and lesson toggle
    const card_title = e.target.closest('.card-title');
    if (card_title) {
        titleClick(card_title);
    }

    /* TOGGLE COURSE DESCRIPTION */
    if(e.target.classList.contains('toggle-desc')){
        e.preventDefault();
        const container = e.target.closest('.course-description');
        if(!container) return;

        const text = container.querySelector('.desc-text');
        const shortText = container.dataset.short;
        const fullText  = container.dataset.full;
        if(text.dataset.expanded === "true"){

            text.innerText = shortText;
            text.dataset.expanded = "false";
            e.target.textContent = "Read more";

        }
        else{

            text.innerHTML = fullText;
            text.dataset.expanded = "true";
            e.target.textContent = "Read less";
        }
    }


    // REGISTER A COURSE
    if(e.target.classList.contains('enroll-btn')){

        const btn = e.target;
        const courseId = btn.dataset.course;

        fetch('/dashboard/courses/course-registration', {
            method:'POST',
            headers:{
                'Content-Type':'application/x-www-form-urlencoded',
                'X-Requested-With':'XMLHttpRequest'
            },
            body:'course_id=' + courseId
        })
        .then(res => res.json())
        .then(data => {

            if(data.success){

                btn.textContent = "✔ Enrolled";
                btn.classList.remove('enroll-btn');
                btn.classList.add('enrolled');
                btn.disabled = true;

            }
            else{
                alert(data.message);
            }

        })
        .catch(err => console.error(err));
    }


    /* LOAD MODULES */
    if ( e.target.classList.contains('course-modules')) {
        
        const heading = e.target.dataset.heading;
        const url = e.target.dataset.url;

        // Send heading to the server for session setup
        fetch('/dashboard/courses/heading', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ heading })
        })
        .then(res => res.json())
        .then(() => {
            // Load modules after session is set
            loadPage(url);
        });
    }


    /* LOAD LESSONS */
    if ( e.target.classList.contains('module-lessons')) {
        const url = e.target.dataset.url;
        loadPage(url);
    }

    /* TOGGLE THE CREATE FORM */
    if(e.target.classList.contains('toggle-btn')){
        const form_type = e.target.dataset.type;
        const wrapper = toggleForm(form_type, e);
        if(!wrapper) return;
    }


    /* CREATE, READ, UPDATE, OR DELETE (CRUD) DATA */
    if(e.target.classList.contains('create-btn') || e.target.classList.contains('save-btn') || e.target.classList.contains('cancel-btn')) {

        e.preventDefault();
        // ✅ get the correct parent card
        let card;
        if(e.target.classList.contains('create-btn')) {card = e.target.closest('.course-form');}
        else {card = e.target.closest('.data-card');}
        const form = e.target.closest('.course-form');
        const data_id = card.dataset.data_id;
        const url = card.dataset.url;
        const type =  e.target.dataset.type || card.dataset.type;
        const goal = e.target.dataset.goal;

        // Cancel the process
        if(goal === 'cancel'){ loadPage(url); }

        let fields = {
            type,
            goal,
            data_id
        };

        if(type === 'course'){
            fields = {
                ...fields,
                code: form.querySelector('#code').value,
                title: form.querySelector('#title').value,
                description: form.querySelector('#description').value,
                prerequisites: form.querySelector('#prerequisites').value,
                duration: form.querySelector('#duration').value,
                registration_status: form.querySelector('#registration_status').value
            };
        }

        if(type === 'module'){
            fields = {
                ...fields,
                course_id: form.querySelector('#course_id').value,
                topic: form.querySelector('#topic').value,
                week: form.querySelector('#week').value,
                stage: form.querySelector('#stage').value,
                days: form.querySelector('#days').value,
                description: form.querySelector('#description').value
            };
        }

        if(type === 'lesson'){
            fields = {
                ...fields,
                module_id: form.querySelector('#module_id').value,
                title: form.querySelector('#title').value,
                overview: form.querySelector('#overview').value,
                introduction: form.querySelector('#introduction').value,
                readings: form.querySelector('#readings').value,
                videos: form.querySelector('#videos').value,
                discussion_question: form.querySelector('#discussion_question').value,
                discussion_answer: form.querySelector('#discussion_answer').value,
                assignment_question: form.querySelector('#assignment_question').value,
                assignment_answer: form.querySelector('#assignment_answer').value
            };
        }

        if(type === 'klines'){
            fields = {
                ...fields,
                aim: form.querySelector('#aim').value,
                interval: form.querySelector('#interval').value
            };
        }

        // Creating the entity
        createEntity(type, url, fields);
    }
    

    /* SHOW EDIT ENTITY FORM */
    if (e.target.classList.contains('edit-btn')) {

        const course_card = e.target.closest('.course-card');
        const data_card = e.target.closest('.data-card');
        const formWrapper = course_card.querySelector('.course-form-wrapper');
        const cardList = course_card.querySelector('.card-list');

        if (formWrapper && cardList) {
            cardList.style.display = "none";          // ✅ hide only content
            formWrapper.classList.toggle('open');      // ✅ show form
            data_card.classList.add('editing');
        }
    }

    /* DELETE DATA */
    if(e.target.classList.contains('delete-btn')){
        const card = e.target.closest('.data-card');
        const type = card.dataset.type;
        const data_id = card.dataset.data_id;
        const table = e.target.dataset.table;
        const url = card.dataset.url;
        const goal = e.target.dataset.goal;

        if(!confirm("Delete this " + type + "?")) return;

        fetch('/dashboard/courses/crud/delete-' + type,{
            method:'POST',
            headers:{
                'Content-Type':'application/x-www-form-urlencoded',
                'X-Requested-With':'XMLHttpRequest'
            },
            body: 'data_id=' + data_id + '&table=' + table + '&type=' + type + '&goal=' + goal
        })
        .then(res => res.json())
        .then(data => {

            if(data.success){
                card.remove();
                loadPage(url);
            }
            else{
                alert(data.message);
            }
        });
    }
});


document.querySelectorAll('.course-form-wrapper.open').forEach(el=>{
    el.classList.remove('open');
});

document.querySelectorAll('.data-card.editing').forEach(el=>{
    el.classList.remove('editing');
});
