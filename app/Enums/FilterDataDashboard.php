<?php

namespace App\Enums;

class FilterDataDashboard extends Enum
{
    const inbox_sender_user = 'Inbox Sender User';

    const inbox_sender_info = 'Inbox Sender Info';

    const inbox_send = 'Inbox Send';

    const inbox_draft = 'Inbox Draft';

    const new_inserted_surplus = 'New inserted surplus';

    const new_inserted_wanted = 'New inserted wanted';

    const new_inserted_contacts_institutes = 'New inserted contacts/institutes';

    const offers_sent = 'Offers Sent';

    const new_orders = 'New Orders';

    const tasks_have_been_sent_out = 'Tasks that have been sent out';

    const offers_approve = 'Offers to approve';

    const offers_remind = 'Offers to remind';

    const offers_inquiry = 'Offers to inquiry';

    const tasks_by_me_user = 'Tasks to do by me user';

    const tasks_by_me_today_user = 'Tasks to do today by me user';

    const tasks_over_time_remind = 'Tasks over time to remind';
}
