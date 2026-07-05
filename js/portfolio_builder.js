// ==========================================================================
// AuraResume Portfolio & Resume Builder Logic
// ==========================================================================

// 1. Initial State (Pre-populated with high-converting Internship-winning data)
const defaultResumeData = {
    profile: {
        name: "Vinuka",
        title: "Full-Stack Software Developer Intern",
        email: "vinuka@example.com",
        phone: "+94 77 123 4567",
        location: "Negombo, Sri Lanka",
        website: "https://vinuka.dev",
        github: "github.com/vinuka-dev",
        linkedin: "linkedin.com/in/vinuka"
    },
    summary: "Dedicated and detail-oriented Full-Stack Developer seeking an internship. Proven capability in building responsive web applications using PHP, MySQL, and JavaScript, with a strong focus on security, document automation, and clean user experience.",
    experience: [
        {
            title: "Web Development Specialist (Freelance)",
            company: "Self-Employed",
            date: "Jun 2024 - Present",
            description: "Developed and launched responsive client sites using PHP and native JavaScript.\nIntegrated MySQL databases for booking systems and administrative management panels.\nLeveraged modern CSS to provide sleek user interfaces with dynamic micro-interactions."
        }
    ],
    projects: [
        {
            title: "AsanTravels - Travel & Itinerary Platform",
            organization: "Independent Project",
            date: "Jan 2026 - Present",
            description: "Developed a PHP/MySQL booking web app with custom day-by-day travel planners and administrative panels.\nEngineered an automated XML/PHPWord engine to dynamically generate styled Word (.docx) itinerary receipts.\nIntegrated secure session authentication and input validation filters to prevent SQL Injection (SQLi) vulnerabilities.\nDesigned responsive UI layouts using native JavaScript (ES6+) and CSS to improve mobile booking UX."
        }
    ],
    education: [
        {
            degree: "Diploma in Software Engineering",
            school: "University of Engineering Process Cohort",
            date: "Jan 2023 - Dec 2025",
            description: "Coursework in Database Design, Object-Oriented Programming (OOP), and Web Application Development.\nParticipated in hands-on cohorts for systems automation and cloud deployment."
        }
    ],
    skills: {
        languages: "PHP, JavaScript (ES6+), SQL (MySQL), HTML5, CSS3, XML",
        tools: "Git, GitHub, XAMPP, Composer, PHPWord, VS Code, Apache",
        concepts: "Full-Stack Development, Database Schema Design, Document Automation, Web Security (CSRF/SQLi prevention)"
    },
    theme: {
        accent: "#dfc384",
        font: "font-sans",
        margin: "margin-normal"
    }
};

let resumeData = JSON.parse(localStorage.getItem('aura_resume_data')) || { ...defaultResumeData };

// 2. DOM Elements
const sheet = document.getElementById('resume-sheet');
const tabButtons = document.querySelectorAll('.tab-btn');
const tabContents = document.querySelectorAll('.tab-content');
const colorButtons = document.querySelectorAll('.color-btn');
const fontSelector = document.getElementById('setting-font');
const marginSelector = document.getElementById('setting-margin');

// 3. Tab Navigation
tabButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        tabButtons.forEach(b => b.classList.remove('active'));
        tabContents.forEach(c => c.classList.remove('active'));

        btn.classList.add('active');
        const tabId = btn.getAttribute('data-tab');
        document.getElementById(tabId).classList.add('active');
    });
});

// 4. Synchronization Inputs -> State -> Preview
function syncInputsToState() {
    // Profile
    resumeData.profile.name = document.getElementById('prof-name').value;
    resumeData.profile.title = document.getElementById('prof-title').value;
    resumeData.profile.email = document.getElementById('prof-email').value;
    resumeData.profile.phone = document.getElementById('prof-phone').value;
    resumeData.profile.location = document.getElementById('prof-location').value;
    resumeData.profile.website = document.getElementById('prof-website').value;
    resumeData.profile.github = document.getElementById('prof-github').value;
    resumeData.profile.linkedin = document.getElementById('prof-linkedin').value;

    // Skills
    resumeData.skills.languages = document.getElementById('skills-langs').value;
    resumeData.skills.tools = document.getElementById('skills-tools').value;
    resumeData.skills.concepts = document.getElementById('skills-concepts').value;

    saveData();
    renderPreview();
    runAtsAssessment();
}

function loadStateToInputs() {
    // Profile
    document.getElementById('prof-name').value = resumeData.profile.name || "";
    document.getElementById('prof-title').value = resumeData.profile.title || "";
    document.getElementById('prof-email').value = resumeData.profile.email || "";
    document.getElementById('prof-phone').value = resumeData.profile.phone || "";
    document.getElementById('prof-location').value = resumeData.profile.location || "";
    document.getElementById('prof-website').value = resumeData.profile.website || "";
    document.getElementById('prof-github').value = resumeData.profile.github || "";
    document.getElementById('prof-linkedin').value = resumeData.profile.linkedin || "";

    // Skills
    document.getElementById('skills-langs').value = resumeData.skills.languages || "";
    document.getElementById('skills-tools').value = resumeData.skills.tools || "";
    document.getElementById('skills-concepts').value = resumeData.skills.concepts || "";

    // Theme inputs
    fontSelector.value = resumeData.theme.font;
    marginSelector.value = resumeData.theme.margin;
    
    colorButtons.forEach(btn => {
        if (btn.getAttribute('data-color').toLowerCase() === resumeData.theme.accent.toLowerCase()) {
            colorButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        }
    });

    // Dynamic Lists (Experience, Projects, Education)
    renderListEditor('experience', 'experience-list', createExperienceCard);
    renderListEditor('projects', 'projects-list', createProjectCard);
    renderListEditor('education', 'education-list', createEducationCard);
}

// 5. Dynamic List Editors
function renderListEditor(type, containerId, cardCreatorFunc) {
    const container = document.getElementById(containerId);
    container.innerHTML = "";
    
    const items = resumeData[type] || [];
    items.forEach((item, index) => {
        const card = cardCreatorFunc(item, index);
        container.appendChild(card);
    });
}

function createExperienceCard(item, index) {
    const card = document.createElement('div');
    card.className = 'editor-card';
    card.innerHTML = `
        <div class="card-actions">
            <button class="btn-card-move" onclick="moveItem('experience', ${index}, -1)" title="Move Up"><i class="fa-solid fa-arrow-up"></i></button>
            <button class="btn-card-move" onclick="moveItem('experience', ${index}, 1)" title="Move Down"><i class="fa-solid fa-arrow-down"></i></button>
            <button class="btn-card-del" onclick="deleteItem('experience', ${index})" title="Delete"><i class="fa-solid fa-trash"></i></button>
        </div>
        <div class="form-group">
            <label>Job Title</label>
            <input type="text" value="${item.title || ''}" oninput="updateItem('experience', ${index}, 'title', this.value)">
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Company / Employer</label>
                <input type="text" value="${item.company || ''}" oninput="updateItem('experience', ${index}, 'company', this.value)">
            </div>
            <div class="form-group">
                <label>Date Period</label>
                <input type="text" value="${item.date || ''}" placeholder="e.g. Jan 2024 - Present" oninput="updateItem('experience', ${index}, 'date', this.value)">
            </div>
        </div>
        <div class="form-group">
            <label>Description Bullets (One per line)</label>
            <textarea oninput="updateItem('experience', ${index}, 'description', this.value)">${item.description || ''}</textarea>
        </div>
    `;
    return card;
}

function createProjectCard(item, index) {
    const card = document.createElement('div');
    card.className = 'editor-card';
    card.innerHTML = `
        <div class="card-actions">
            <button class="btn-card-move" onclick="moveItem('projects', ${index}, -1)" title="Move Up"><i class="fa-solid fa-arrow-up"></i></button>
            <button class="btn-card-move" onclick="moveItem('projects', ${index}, 1)" title="Move Down"><i class="fa-solid fa-arrow-down"></i></button>
            <button class="btn-card-del" onclick="deleteItem('projects', ${index})" title="Delete"><i class="fa-solid fa-trash"></i></button>
        </div>
        <div class="form-group">
            <label>Project Title</label>
            <input type="text" value="${item.title || ''}" oninput="updateItem('projects', ${index}, 'title', this.value)">
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Organization / Subheading</label>
                <input type="text" value="${item.organization || ''}" oninput="updateItem('projects', ${index}, 'organization', this.value)">
            </div>
            <div class="form-group">
                <label>Date Period</label>
                <input type="text" value="${item.date || ''}" placeholder="e.g. Jan 2026 - Present" oninput="updateItem('projects', ${index}, 'date', this.value)">
            </div>
        </div>
        <div class="form-group">
            <label>Description Bullets (One per line)</label>
            <textarea oninput="updateItem('projects', ${index}, 'description', this.value)">${item.description || ''}</textarea>
        </div>
    `;
    return card;
}

function createEducationCard(item, index) {
    const card = document.createElement('div');
    card.className = 'editor-card';
    card.innerHTML = `
        <div class="card-actions">
            <button class="btn-card-move" onclick="moveItem('education', ${index}, -1)" title="Move Up"><i class="fa-solid fa-arrow-up"></i></button>
            <button class="btn-card-move" onclick="moveItem('education', ${index}, 1)" title="Move Down"><i class="fa-solid fa-arrow-down"></i></button>
            <button class="btn-card-del" onclick="deleteItem('education', ${index})" title="Delete"><i class="fa-solid fa-trash"></i></button>
        </div>
        <div class="form-group">
            <label>Degree / Certificate</label>
            <input type="text" value="${item.degree || ''}" oninput="updateItem('education', ${index}, 'degree', this.value)">
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>School / Institution</label>
                <input type="text" value="${item.school || ''}" oninput="updateItem('education', ${index}, 'school', this.value)">
            </div>
            <div class="form-group">
                <label>Date Period</label>
                <input type="text" value="${item.date || ''}" placeholder="e.g. 2023 - 2025" oninput="updateItem('education', ${index}, 'date', this.value)">
            </div>
        </div>
        <div class="form-group">
            <label>Additional Details (One per line)</label>
            <textarea oninput="updateItem('education', ${index}, 'description', this.value)">${item.description || ''}</textarea>
        </div>
    `;
    return card;
}

// Item actions
window.updateItem = function(type, index, field, value) {
    resumeData[type][index][field] = value;
    saveData();
    renderPreview();
    runAtsAssessment();
};

window.deleteItem = function(type, index) {
    resumeData[type].splice(index, 1);
    saveData();
    loadStateToInputs();
    renderPreview();
    runAtsAssessment();
};

window.moveItem = function(type, index, direction) {
    const targetIndex = index + direction;
    if (targetIndex < 0 || targetIndex >= resumeData[type].length) return;
    
    const temp = resumeData[type][index];
    resumeData[type][index] = resumeData[type][targetIndex];
    resumeData[type][targetIndex] = temp;
    
    saveData();
    loadStateToInputs();
    renderPreview();
};

// Add item listeners
document.getElementById('add-exp-btn').addEventListener('click', () => {
    resumeData.experience.push({ title: "New Role", company: "Company", date: "Present", description: "Bullet point details." });
    loadStateToInputs();
    renderPreview();
});

document.getElementById('add-proj-btn').addEventListener('click', () => {
    resumeData.projects.push({ title: "New Project", organization: "Self-Project", date: "Present", description: "Bullet point details." });
    loadStateToInputs();
    renderPreview();
});

document.getElementById('add-edu-btn').addEventListener('click', () => {
    resumeData.education.push({ degree: "Qualification", school: "Institution", date: "Present", description: "Details." });
    loadStateToInputs();
    renderPreview();
});

// 6. Preview Renderer
function renderPreview() {
    // Theme configurations
    sheet.className = `resume-sheet ${resumeData.theme.font} ${resumeData.theme.margin}`;
    sheet.style.setProperty('--accent-tone', resumeData.theme.accent);

    let html = "";

    // A. Header
    html += `
        <header class="doc-header">
            <h1>${escapeHtml(resumeData.profile.name)}</h1>
            <div class="doc-subtitle">${escapeHtml(resumeData.profile.title)}</div>
            <div class="doc-contacts">
                ${resumeData.profile.email ? `<span><i class="fa-solid fa-envelope"></i> <a href="mailto:${resumeData.profile.email}">${escapeHtml(resumeData.profile.email)}</a></span>` : ''}
                ${resumeData.profile.phone ? `<span><i class="fa-solid fa-phone"></i> ${escapeHtml(resumeData.profile.phone)}</span>` : ''}
                ${resumeData.profile.location ? `<span><i class="fa-solid fa-location-dot"></i> ${escapeHtml(resumeData.profile.location)}</span>` : ''}
                ${resumeData.profile.website ? `<span><i class="fa-solid fa-globe"></i> <a href="${resumeData.profile.website}" target="_blank">${escapeHtml(resumeData.profile.website.replace(/^https?:\/\//, ''))}</a></span>` : ''}
                ${resumeData.profile.github ? `<span><i class="fa-brands fa-github"></i> <a href="https://${resumeData.profile.github}" target="_blank">${escapeHtml(resumeData.profile.github)}</a></span>` : ''}
                ${resumeData.profile.linkedin ? `<span><i class="fa-brands fa-linkedin"></i> <a href="https://${resumeData.profile.linkedin}" target="_blank">${escapeHtml(resumeData.profile.linkedin)}</a></span>` : ''}
            </div>
        </header>
    `;

    // B. Summary / Profile Statement
    if (resumeData.summary) {
        html += `
            <div class="doc-summary">
                ${escapeHtml(resumeData.summary)}
            </div>
        `;
    }

    // C. Education
    if (resumeData.education && resumeData.education.length > 0) {
        html += `
            <section class="doc-section">
                <h2 class="doc-section-title">Education</h2>
        `;
        resumeData.education.forEach(edu => {
            html += `
                <div class="doc-item">
                    <div class="doc-item-row">
                        <span class="doc-item-title">${escapeHtml(edu.degree)}</span>
                        <span class="doc-item-date">${escapeHtml(edu.date)}</span>
                    </div>
                    <div class="doc-item-subhead">${escapeHtml(edu.school)}</div>
                    ${renderBulletPoints(edu.description)}
                </div>
            `;
        });
        html += `</section>`;
    }

    // D. Work Experience
    if (resumeData.experience && resumeData.experience.length > 0) {
        html += `
            <section class="doc-section">
                <h2 class="doc-section-title">Experience</h2>
        `;
        resumeData.experience.forEach(exp => {
            html += `
                <div class="doc-item">
                    <div class="doc-item-row">
                        <span class="doc-item-title">${escapeHtml(exp.title)}</span>
                        <span class="doc-item-date">${escapeHtml(exp.date)}</span>
                    </div>
                    <div class="doc-item-subhead">${escapeHtml(exp.company)}</div>
                    ${renderBulletPoints(exp.description)}
                </div>
            `;
        });
        html += `</section>`;
    }

    // E. Projects (Exact user request layout styling)
    if (resumeData.projects && resumeData.projects.length > 0) {
        html += `
            <section class="doc-section">
                <h2 class="doc-section-title">Projects</h2>
        `;
        resumeData.projects.forEach(proj => {
            html += `
                <div class="doc-item">
                    <div class="doc-item-row">
                        <span class="doc-item-title">${escapeHtml(proj.title)}</span>
                        <span class="doc-item-date">${escapeHtml(proj.date)}</span>
                    </div>
                    <div class="doc-item-subhead">${escapeHtml(proj.organization)}</div>
                    ${renderBulletPoints(proj.description)}
                </div>
            `;
        });
        html += `</section>`;
    }

    // F. Technical Skills
    const hasSkills = resumeData.skills.languages || resumeData.skills.tools || resumeData.skills.concepts;
    if (hasSkills) {
        html += `
            <section class="doc-section">
                <h2 class="doc-section-title">Skills</h2>
        `;
        if (resumeData.skills.languages) {
            html += `
                <div class="doc-skills-row">
                    <span class="doc-skills-category">Languages:</span> ${escapeHtml(resumeData.skills.languages)}
                </div>
            `;
        }
        if (resumeData.skills.tools) {
            html += `
                <div class="doc-skills-row">
                    <span class="doc-skills-category">Developer Tools & Systems:</span> ${escapeHtml(resumeData.skills.tools)}
                </div>
            `;
        }
        if (resumeData.skills.concepts) {
            html += `
                <div class="doc-skills-row">
                    <span class="doc-skills-category">Concepts & Knowledge:</span> ${escapeHtml(resumeData.skills.concepts)}
                </div>
            `;
        }
        html += `</section>`;
    }

    sheet.innerHTML = html;
}

function renderBulletPoints(bulletText) {
    if (!bulletText) return "";
    const lines = bulletText.split('\n').map(l => l.trim()).filter(l => l.length > 0);
    if (lines.length === 0) return "";
    
    let html = `<ul class="doc-bullets">`;
    lines.forEach(line => {
        // Strip any initial bullet characters if entered by user (e.g. -, *, •)
        const cleanLine = line.replace(/^[\s\-\*\•\+]+/, '');
        html += `<li>${escapeHtml(cleanLine)}</li>`;
    });
    html += `</ul>`;
    return html;
}

function escapeHtml(text) {
    if (!text) return "";
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// 7. ATS Optimization Calculator
function runAtsAssessment() {
    let score = 0;
    const tips = [];

    // Profile Checks
    if (resumeData.profile.name) score += 10;
    else tips.push({ type: 'xmark', text: "Full Name is missing." });

    if (resumeData.profile.title) score += 10;
    else tips.push({ type: 'warning', text: "Target CV Title is missing (highly recommended)." });

    if (resumeData.profile.email && resumeData.profile.email.includes('@')) score += 10;
    else tips.push({ type: 'xmark', text: "Provide a valid email address." });

    if (resumeData.profile.phone) score += 5;
    else tips.push({ type: 'warning', text: "Phone number is missing." });

    if (resumeData.profile.location) score += 5;
    else tips.push({ type: 'warning', text: "Provide a general location (City, Country)." });

    if (resumeData.profile.linkedin) score += 10;
    else tips.push({ type: 'warning', text: "LinkedIn link is missing. Crucial for tech interns." });

    if (resumeData.profile.github) score += 10;
    else tips.push({ type: 'warning', text: "Add GitHub link to showcase repositories." });

    // Section Content checks
    if (resumeData.projects && resumeData.projects.length > 0) score += 15;
    else tips.push({ type: 'xmark', text: "No projects specified. Add at least 1 detailed project." });

    if (resumeData.education && resumeData.education.length > 0) score += 15;
    else tips.push({ type: 'xmark', text: "Education details are missing." });

    if (resumeData.skills.languages || resumeData.skills.tools) score += 10;
    else tips.push({ type: 'warning', text: "Add a skill summary block listing languages & tools." });

    // Word analysis for bullet points & action verbs
    let bulletCount = 0;
    let actionVerbsFound = false;
    const actionVerbs = [
        'build', 'built', 'develop', 'developed', 'program', 'programmed', 'engineer', 'engineered', 
        'create', 'created', 'design', 'designed', 'implement', 'implemented', 'optimize', 'optimized',
        'integrate', 'integrated', 'structure', 'structured', 'manage', 'managed', 'deploy', 'deployed'
    ];

    const allDescriptions = [
        ...resumeData.experience.map(e => e.description || ""),
        ...resumeData.projects.map(p => p.description || ""),
        ...resumeData.education.map(ed => ed.description || "")
    ].join(" ").toLowerCase();

    // Check action verbs
    actionVerbs.forEach(verb => {
        if (allDescriptions.includes(verb)) {
            actionVerbsFound = true;
        }
    });

    if (actionVerbsFound) score += 10;
    else tips.push({ type: 'warning', text: "Include action verbs (e.g., Developed, Engineered, Optimized) in bullets." });

    // Check count of bullet points
    const lines = allDescriptions.split('\n').filter(l => l.trim().length > 0);
    if (lines.length >= 5) score += 10;
    else tips.push({ type: 'warning', text: "Add more detailed bullet points across sections (aim for 5+ total)." });

    // Cap score at 100
    const finalScore = Math.min(score, 100);

    // Update Score Circle SVG
    const scoreCircle = document.getElementById('score-circle');
    const scoreText = document.getElementById('score-text');
    const scoreGrade = document.getElementById('score-grade');
    const chart = document.querySelector('.circular-chart');
    
    // Animate the circle
    scoreCircle.style.strokeDasharray = `${finalScore}, 100`;
    scoreText.textContent = `${finalScore}%`;

    // Grade and color coding
    chart.classList.remove('red', 'orange', 'green');
    if (finalScore < 50) {
        chart.classList.add('red');
        scoreGrade.textContent = "Needs Work (Weak CV)";
    } else if (finalScore < 80) {
        chart.classList.add('orange');
        scoreGrade.textContent = "Competitive (Good)";
    } else {
        chart.classList.add('green');
        scoreGrade.textContent = "Excellent (Ready to apply)";
    }

    // List optimization recommendations
    const listElement = document.getElementById('ats-suggestions-list');
    listElement.innerHTML = "";
    
    if (tips.length === 0) {
        listElement.innerHTML = `<li><i class="fa-solid fa-circle-check"></i> Resume is fully optimized! Ready for upload.</li>`;
    } else {
        tips.forEach(tip => {
            const icon = tip.type === 'xmark' 
                ? 'fa-circle-xmark' 
                : (tip.type === 'warning' ? 'fa-triangle-exclamation' : 'fa-circle-check');
            
            const li = document.createElement('li');
            li.innerHTML = `<i class="fa-solid ${icon}"></i> <span>${tip.text}</span>`;
            listElement.appendChild(li);
        });
    }
}

// 8. Theme Options
colorButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        colorButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        
        resumeData.theme.accent = btn.getAttribute('data-color');
        saveData();
        renderPreview();
    });
});

fontSelector.addEventListener('change', () => {
    resumeData.theme.font = fontSelector.value;
    saveData();
    renderPreview();
});

marginSelector.addEventListener('change', () => {
    resumeData.theme.margin = marginSelector.value;
    saveData();
    renderPreview();
});

// 9. Persistance helpers
function saveData() {
    localStorage.setItem('aura_resume_data', JSON.stringify(resumeData));
}

// 10. Buttons Event Handlers
document.getElementById('btnPrint').addEventListener('click', () => {
    window.print();
});

document.getElementById('btnReset').addEventListener('click', () => {
    if (confirm("Reset layout and load standard internship CV data? This will overwrite current text.")) {
        resumeData = JSON.parse(JSON.stringify(defaultResumeData));
        saveData();
        loadStateToInputs();
        renderPreview();
        runAtsAssessment();
    }
});

// Import/Export JSON
document.getElementById('btnExportJSON').addEventListener('click', () => {
    const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(resumeData, null, 2));
    const downloadAnchor = document.createElement('a');
    downloadAnchor.setAttribute("href", dataStr);
    downloadAnchor.setAttribute("download", `${resumeData.profile.name || 'resume'}_cv_data.json`);
    document.body.appendChild(downloadAnchor);
    downloadAnchor.click();
    downloadAnchor.remove();
});

document.getElementById('btnImportJSON').addEventListener('click', () => {
    document.getElementById('importFile').click();
});

document.getElementById('importFile').addEventListener('change', (event) => {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(e) {
        try {
            const parsed = JSON.parse(e.target.result);
            if (parsed.profile && parsed.theme) {
                resumeData = parsed;
                saveData();
                loadStateToInputs();
                renderPreview();
                runAtsAssessment();
                alert("CV Data successfully restored!");
            } else {
                alert("Invalid resume JSON structure.");
            }
        } catch (err) {
            alert("Error parsing JSON file: " + err.message);
        }
    };
    reader.readAsText(file);
});

// Bind form element events
function bindEvents() {
    const textInputs = document.querySelectorAll('#tab-profile input, #tab-skills input');
    textInputs.forEach(input => {
        input.addEventListener('input', syncInputsToState);
    });
}

// Initialize Application
window.addEventListener('DOMContentLoaded', () => {
    loadStateToInputs();
    renderPreview();
    runAtsAssessment();
    bindEvents();
});
