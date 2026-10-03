<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal Hub - Registration</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="page">

    <div class="topbar">
        <div class="logo">SPH</div>

        <div>
            <h1>Student Portal Hub</h1>
            <p>Campus Registration Center</p>
        </div>
    </div>

    <div class="main-card">

        <div class="intro">

            <span class="tag">NEW STUDENT</span>

            <h2>Let's get you registered.</h2>

            <p>
                Enter your basic details below to create your
                student registration record.
            </p>

            <div class="info-box">
                <div class="info-number">01</div>
                <div>
                    <strong>Student Details</strong>
                    <p>Provide accurate information.</p>
                </div>
            </div>

            <div class="info-box">
                <div class="info-number">02</div>
                <div>
                    <strong>Choose Course</strong>
                    <p>Select your academic course.</p>
                </div>
            </div>

            <div class="info-box">
                <div class="info-number">03</div>
                <div>
                    <strong>Submit</strong>
                    <p>Your data will be recorded.</p>
                </div>
            </div>

        </div>

        <div class="form-section">

            <div class="form-heading">
                <p>REGISTRATION FORM</p>
                <h2>Student Information</h2>
            </div>

            <form action="process.php" method="POST">

                <div class="form-row">

                    <div class="field">
                        <label for="name">Full Name</label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            placeholder="e.g. Dhruv Patel"
                            required
                        >
                    </div>

                    <div class="field">
                        <label for="email">Email Address</label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="student@example.com"
                            required
                        >
                    </div>

                </div>

                <div class="form-row">

                    <div class="field">
                        <label for="mobile">Mobile Number</label>

                        <input
                            type="text"
                            id="mobile"
                            name="mobile"
                            placeholder="10 digit mobile number"
                            required
                        >
                    </div>

                    <div class="field">
                        <label for="year">Academic Year</label>

                        <select id="year" name="year" required>

                            <option value="">Choose year</option>

                            <option value="1st Year">
                                1st Year
                            </option>

                            <option value="2nd Year">
                                2nd Year
                            </option>

                            <option value="3rd Year">
                                3rd Year
                            </option>

                            <option value="4th Year">
                                4th Year
                            </option>

                        </select>
                    </div>

                </div>

                <div class="field">

                    <label>Select Course</label>

                    <div class="course-options">

                        <label class="course">
                            <input
                                type="radio"
                                name="course"
                                value="BCA"
                                required
                            >
                            <span>BCA</span>
                        </label>

                        <label class="course">
                            <input
                                type="radio"
                                name="course"
                                value="B.Sc IT"
                            >
                            <span>B.Sc IT</span>
                        </label>

                        <label class="course">
                            <input
                                type="radio"
                                name="course"
                                value="BBA"
                            >
                            <span>BBA</span>
                        </label>

                        <label class="course">
                            <input
                                type="radio"
                                name="course"
                                value="MCA"
                            >
                            <span>MCA</span>
                        </label>

                    </div>

                </div>

                <div class="field">

                    <label for="message">Message</label>

                    <textarea
                        id="message"
                        name="message"
                        rows="4"
                        placeholder="Write a short message..."
                        required
                    ></textarea>

                </div>

                <button type="submit">
                    Submit Registration
                    <span>→</span>
                </button>

                <p class="secure-text">
                    Your information will be stored in the student records file.
                </p>

            </form>

        </div>

    </div>

</div>

</body>
</html>