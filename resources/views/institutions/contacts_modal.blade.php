<!-- Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="Filter institutions"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Contacts</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div id="spinner" class="visible d-flex justify-content-center">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <ul class="visible list-group" id="contactsList">
                    <li class="list-group-item"></li>
                </ul>
            </div>
        </div>
    </div>
    <input type="hidden" id="contact_details_organisation_id" value="0"/>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        document.getElementById("contact_details_organisation_id")
            .addEventListener("change", e => {
                let parentElement = document.getElementById("contactsList");
                let organisationId = e.target.value;

                parentElement.innerHTML = "";

                document.getElementById("spinner").classList.add("visible");
                document.getElementById("spinner").classList.remove("invisible");
                document.getElementById("contactsList").classList.add("invisible");
                document.getElementById("contactsList").classList.remove("visible");

                axios.get(`/organisations/contactsForOrganisation/${organisationId}`)
                    .then(res => {
                        if (res.data.data.length > 0) {
                            res.data.data.forEach(contact => {
                                let listElement = document.createElement("li");
                                listElement.classList.add("list-group-item");
                                let name = (contact?.first_name ? contact?.first_name : "") + (contact?.last_name ? ` ${contact?.last_name}` : "");
                                let email = contact?.email ? ` - ${contact.email}` : "";
                                listElement.textContent = `${name}${email}`;
                                parentElement.append(listElement);
                            });
                        } else {
                            let listElement = document.createElement("li");
                            listElement.classList.add("list-group-item");
                            listElement.textContent = `No contacts found for ${res.data.organisation.name}`;
                            parentElement.append(listElement);
                        }
                    })
                    .finally(() => {
                        document.getElementById("contactsList").classList.add("visible");
                        document.getElementById("contactsList").classList.remove("invisible");
                        document.getElementById("spinner").classList.add("invisible");
                        document.getElementById("spinner").classList.remove("visible");
                    });
            });
    });
</script>
