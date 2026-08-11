/* ==========================================================================
   Student Grading Management System — MOCK DATA ONLY
   No database, no API calls. Everything the UI needs lives in this file.
   Replace these arrays with real data from a backend later; view markup
   and app.js rendering logic will not need to change.
   ========================================================================== */

const demoAccounts = [
    {
        email: "admin@example.com",
        role: "admin",
        name: "Dr. Ramon Villareal",
        redirect: "/admin/dashboard",
    },
    {
        email: "teacher@example.com",
        role: "teacher",
        name: "Prof. Andrea Reyes",
        redirect: "/teacher/dashboard",
    },
    {
        email: "student@example.com",
        role: "student",
        name: "Juan Dela Cruz",
        redirect: "/student/dashboard",
    },
];

const schoolInfo = {
    name: "Northbridge Institute of Technology",
    shortName: "NBIT",
    address: "123 Rizal Avenue, Imus, Cavite, Philippines",
    schoolYear: "2026–2027",
    semester: "First Semester",
};

const students = [
    {
        id: "2026-0001",
        name: "Juan Dela Cruz",
        course: "BSIT",
        year: "1st Year",
        section: "BSIT 1-A",
        status: "Active",
        email: "juan.delacruz@nbit.edu.ph",
    },
    {
        id: "2026-0002",
        name: "Maria Santos",
        course: "BSIT",
        year: "1st Year",
        section: "BSIT 1-A",
        status: "Active",
        email: "maria.santos@nbit.edu.ph",
    },
    {
        id: "2026-0003",
        name: "Pedro Ramirez",
        course: "BSIT",
        year: "1st Year",
        section: "BSIT 1-A",
        status: "Active",
        email: "pedro.ramirez@nbit.edu.ph",
    },
    {
        id: "2026-0004",
        name: "Angela Cruz",
        course: "BSIT",
        year: "1st Year",
        section: "BSIT 1-A",
        status: "Inactive",
        email: "angela.cruz@nbit.edu.ph",
    },
    {
        id: "2026-0005",
        name: "Michael Torres",
        course: "BSIT",
        year: "2nd Year",
        section: "BSIT 2-A",
        status: "Active",
        email: "michael.torres@nbit.edu.ph",
    },
    {
        id: "2026-0006",
        name: "Kristine Mercado",
        course: "BSIT",
        year: "2nd Year",
        section: "BSIT 2-A",
        status: "Active",
        email: "kristine.mercado@nbit.edu.ph",
    },
    {
        id: "2026-0007",
        name: "Joshua Bautista",
        course: "BSIT",
        year: "2nd Year",
        section: "BSIT 2-A",
        status: "Active",
        email: "joshua.bautista@nbit.edu.ph",
    },
    {
        id: "2026-0008",
        name: "Samantha Lopez",
        course: "BSCS",
        year: "1st Year",
        section: "BSCS 1-A",
        status: "Active",
        email: "samantha.lopez@nbit.edu.ph",
    },
    {
        id: "2026-0009",
        name: "Carlo Fernandez",
        course: "BSCS",
        year: "1st Year",
        section: "BSCS 1-A",
        status: "Active",
        email: "carlo.fernandez@nbit.edu.ph",
    },
    {
        id: "2026-0010",
        name: "Bianca Villanueva",
        course: "BSCS",
        year: "3rd Year",
        section: "BSCS 3-A",
        status: "Active",
        email: "bianca.villanueva@nbit.edu.ph",
    },
    {
        id: "2026-0011",
        name: "Renz Aquino",
        course: "BSIT",
        year: "3rd Year",
        section: "BSIT 3-A",
        status: "Active",
        email: "renz.aquino@nbit.edu.ph",
    },
    {
        id: "2026-0012",
        name: "Faith Navarro",
        course: "BSIT",
        year: "3rd Year",
        section: "BSIT 3-A",
        status: "Suspended",
        email: "faith.navarro@nbit.edu.ph",
    },
];

const teachers = [
    {
        id: "T-0001",
        name: "Prof. Andrea Reyes",
        email: "andrea.reyes@nbit.edu.ph",
        department: "College of Computer Studies",
        subjects: ["Programming 1", "Web Development"],
        status: "Active",
    },
    {
        id: "T-0002",
        name: "Prof. Miguel Santos",
        email: "miguel.santos@nbit.edu.ph",
        department: "College of Computer Studies",
        subjects: ["Database Management"],
        status: "Active",
    },
    {
        id: "T-0003",
        name: "Prof. Liza Domingo",
        email: "liza.domingo@nbit.edu.ph",
        department: "College of Computer Studies",
        subjects: ["Networking", "Introduction to Computing"],
        status: "Active",
    },
    {
        id: "T-0004",
        name: "Prof. Ramon Cortez",
        email: "ramon.cortez@nbit.edu.ph",
        department: "General Education",
        subjects: ["Mathematics in the Modern World"],
        status: "On Leave",
    },
];

const subjects = [
    {
        code: "IT101",
        name: "Introduction to Computing",
        units: 3,
        department: "College of Computer Studies",
        status: "Active",
    },
    {
        code: "IT102",
        name: "Programming 1",
        units: 3,
        department: "College of Computer Studies",
        status: "Active",
    },
    {
        code: "IT201",
        name: "Database Management",
        units: 3,
        department: "College of Computer Studies",
        status: "Active",
    },
    {
        code: "IT202",
        name: "Web Development",
        units: 3,
        department: "College of Computer Studies",
        status: "Active",
    },
    {
        code: "IT301",
        name: "Networking",
        units: 3,
        department: "College of Computer Studies",
        status: "Active",
    },
    {
        code: "GE101",
        name: "Mathematics in the Modern World",
        units: 3,
        department: "General Education",
        status: "Inactive",
    },
];

const sections = [
    {
        name: "BSIT 1-A",
        course: "BSIT",
        year: "1st Year",
        adviser: "Prof. Andrea Reyes",
        students: 45,
        status: "Active",
    },
    {
        name: "BSIT 2-A",
        course: "BSIT",
        year: "2nd Year",
        adviser: "Prof. Miguel Santos",
        students: 38,
        status: "Active",
    },
    {
        name: "BSIT 3-A",
        course: "BSIT",
        year: "3rd Year",
        adviser: "Prof. Liza Domingo",
        students: 30,
        status: "Active",
    },
    {
        name: "BSCS 1-A",
        course: "BSCS",
        year: "1st Year",
        adviser: "Prof. Ramon Cortez",
        students: 42,
        status: "Active",
    },
    {
        name: "BSCS 3-A",
        course: "BSCS",
        year: "3rd Year",
        adviser: "Prof. Andrea Reyes",
        students: 27,
        status: "Active",
    },
];

// Teacher's assigned classes (for dashboard + "My Classes")
const teacherClasses = [
    {
        subjectCode: "IT102",
        subject: "Programming 1",
        section: "BSIT 1-A",
        students: 45,
        schedule: "MWF 8:00–9:00 AM",
        room: "Rm 301",
        pending: 3,
        submitted: 42,
    },
    {
        subjectCode: "IT201",
        subject: "Database Management",
        section: "BSIT 2-A",
        students: 38,
        schedule: "TTh 1:00–2:30 PM",
        room: "Rm 214",
        pending: 0,
        submitted: 38,
    },
    {
        subjectCode: "IT202",
        subject: "Web Development",
        section: "BSCS 1-A",
        students: 42,
        schedule: "MWF 10:00–11:00 AM",
        room: "Lab 2",
        pending: 12,
        submitted: 30,
    },
];

// Per-class grade sheets, keyed by "subjectCode-section"
const gradeSheets = {
    "IT102-BSIT 1-A": [
        {
            id: "2026-0001",
            name: "Juan Dela Cruz",
            prelim: 88,
            midterm: 90,
            final: 92,
        },
        {
            id: "2026-0002",
            name: "Maria Santos",
            prelim: 85,
            midterm: 87,
            final: 89,
        },
        {
            id: "2026-0003",
            name: "Pedro Ramirez",
            prelim: 78,
            midterm: 74,
            final: 80,
        },
        {
            id: "2026-0004",
            name: "Angela Cruz",
            prelim: 65,
            midterm: 60,
            final: 58,
        },
        {
            id: "2026-0005",
            name: "Michael Torres",
            prelim: 91,
            midterm: 93,
            final: 95,
        },
    ],
    "IT201-BSIT 2-A": [
        {
            id: "2026-0005",
            name: "Michael Torres",
            prelim: 89,
            midterm: 91,
            final: 93,
        },
        {
            id: "2026-0006",
            name: "Kristine Mercado",
            prelim: 92,
            midterm: 90,
            final: 94,
        },
        {
            id: "2026-0007",
            name: "Joshua Bautista",
            prelim: 70,
            midterm: 68,
            final: 72,
        },
    ],
    "IT202-BSCS 1-A": [
        {
            id: "2026-0008",
            name: "Samantha Lopez",
            prelim: 91,
            midterm: 93,
            final: 94,
        },
        {
            id: "2026-0009",
            name: "Carlo Fernandez",
            prelim: 83,
            midterm: 85,
            final: 87,
        },
    ],
};

// The logged-in student's own grades (student.email === student@example.com)
const studentProfile = {
    id: "2026-0001",
    name: "Juan Dela Cruz",
    course: "BSIT — Bachelor of Science in Information Technology",
    yearLevel: "1st Year",
    section: "BSIT 1-A",
    schoolYear: schoolInfo.schoolYear,
    semester: schoolInfo.semester,
    status: "Active",
    email: "juan.delacruz@nbit.edu.ph",
    contact: "0917-123-4567",
    address: "Imus, Cavite, Philippines",
    gwa: 90.4,
};

const studentGrades = [
    {
        code: "IT102",
        subject: "Programming 1",
        units: 3,
        prelim: 88,
        midterm: 90,
        final: 92,
        instructor: "Prof. Andrea Reyes",
    },
    {
        code: "IT201",
        subject: "Database Management",
        units: 3,
        prelim: 85,
        midterm: 87,
        final: 89,
        instructor: "Prof. Miguel Santos",
    },
    {
        code: "IT202",
        subject: "Web Development",
        units: 3,
        prelim: 91,
        midterm: 93,
        final: 94,
        instructor: "Prof. Andrea Reyes",
    },
    {
        code: "IT101",
        subject: "Introduction to Computing",
        units: 3,
        prelim: 79,
        midterm: 82,
        final: 85,
        instructor: "Prof. Liza Domingo",
    },
];

// Academic records (past semesters) for the student
const academicRecords = [
    {
        schoolYear: "2025–2026",
        semester: "Second Semester",
        gwa: 88.7,
        status: "Completed",
        subjects: [
            {
                code: "GE101",
                subject: "Mathematics in the Modern World",
                units: 3,
                grade: 87,
                remarks: "Passed",
            },
            {
                code: "IT101",
                subject: "Introduction to Computing",
                units: 3,
                grade: 90,
                remarks: "Passed",
            },
        ],
    },
    {
        schoolYear: "2025–2026",
        semester: "First Semester",
        gwa: 86.2,
        status: "Completed",
        subjects: [
            {
                code: "GE100",
                subject: "Purposive Communication",
                units: 3,
                grade: 88,
                remarks: "Passed",
            },
            {
                code: "IT100",
                subject: "Computer Fundamentals",
                units: 3,
                grade: 85,
                remarks: "Passed",
            },
        ],
    },
];

// Recent activity feeds
const recentGradeSubmissions = [
    {
        teacher: "Prof. Andrea Reyes",
        subject: "Programming 1",
        section: "BSIT 1-A",
        time: "2 hours ago",
    },
    {
        teacher: "Prof. Miguel Santos",
        subject: "Database Management",
        section: "BSIT 2-A",
        time: "5 hours ago",
    },
    {
        teacher: "Prof. Liza Domingo",
        subject: "Networking",
        section: "BSIT 3-A",
        time: "Yesterday",
    },
];

const recentlyAddedStudents = [
    {
        id: "2026-0011",
        name: "Renz Aquino",
        section: "BSIT 3-A",
        time: "2 days ago",
    },
    {
        id: "2026-0010",
        name: "Bianca Villanueva",
        section: "BSCS 3-A",
        time: "3 days ago",
    },
];

const gradeDistribution = [
    { label: "90–100 (Excellent)", count: 34, color: "#1a8a53" },
    { label: "85–89 (Very Good)", count: 52, color: "#2563a3" },
    { label: "80–84 (Good)", count: 41, color: "#c8952f" },
    { label: "75–79 (Satisfactory)", count: 18, color: "#b56a05" },
    { label: "Below 75 (Failed)", count: 7, color: "#d13438" },
];

const teacherActivities = [
    {
        text: "Submitted final grades for Database Management — BSIT 2-A",
        time: "Today, 9:14 AM",
    },
    {
        text: "Saved draft grades for Web Development — BSCS 1-A",
        time: "Yesterday, 4:02 PM",
    },
    {
        text: "Updated prelim grades for Programming 1 — BSIT 1-A",
        time: "2 days ago",
    },
];
