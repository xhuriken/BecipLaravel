import './bootstrap';
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

import './utils/loader'
import './utils/datatables';
import './usercontroller/edit_save';
import './usercontroller/add_company';
import './home/checkboxtable';
import './project/dropzone';
import './profile/profile'
import './home/modals'
import './home/project_name'
import './file/update_file'
import './home/delete_project'
import './project/delete_file'
import './project/project_actions'
import './project/masks'
import './project/distribution_checkbox'
import './project/comment_modal'
import './profile/forgot_modal'
import './home/delete_empty'
import './usercontroller/add';
import './utils/showpassword';
import './usercontroller/companyselect';


if (!window._fetchOverridden) {
    const originalFetch = window.fetch;

    window.fetch = function (...args) {
        showFetchLoader();
        return originalFetch(...args)
            .finally(() => {
                hideFetchLoader();
            });
    };

    window._fetchOverridden = true;
}
