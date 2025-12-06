# PMPro Cancellation Survey

**A custom WordPress plugin to capture user feedback upon membership cancellation.**


## 📋 Description

**PMPro Cancellation Survey** is a lightweight add-on for Paid Memberships Pro that helps site administrators understand churn.

Instead of allowing an immediate cancellation when a user clicks "Cancel" on their Membership Account page, this plugin intercepts the action and displays a form. The user is asked to select a reason for leaving and provide optional feedback before the cancellation is processed.

### Key Features
- **🚫 Smart Interception:** Prevents accidental cancellations by requiring a confirmation step.
- **💬 Modal Interface:** A clean, modern JavaScript modal (popup) that matches your theme's styling.
- **📝 Feedback Collection:** Captures a specific reason (via radio buttons) and detailed comments.
- **📧 Admin Notifications:** Automatically emails the site administrator with the cancellation reason immediately upon submission.
- Asks the user to input atleast 50 characters.


## 🛠 Installation

Note: You must remove the "Reason for Cancellation" plugin from PMPro to use this.

1. Download the repository or unzip the plugin file.
2. Upload the `pmpro-cancellation-survey` folder to your `/wp-content/plugins/` directory.
3. Activate the plugin through the **Plugins** menu in WordPress.
4. Ensure **Paid Memberships Pro** is installed and active.


## ⚙️ Configuration

Currently, the survey reasons are defined within the plugin code to keep it lightweight. To customize the reasons:

1. Open `pmpro-cancellation-survey.php`.
2. Locate the `$reasons` array (near the top of the file).
3. Edit the strings to match your business needs.

Note: The cancellation surveys adds a form to the cancel page and once the form is submitted the answers are stored in `usermeta` and also sent to administrator via email.
