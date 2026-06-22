import { effect, track } from "ripple";
import { navigateTo } from "ripple-router";

import { notyf } from "../notyf";
import { StatusMessage } from "../views/StatusMessage.tsrx";
import { parseApiData, parseApiError } from "./activitiesPage.utils";

type InviteParams = {
   token?: string;
};

type InviteResponse = {
   activity?: {
      id?: number | string;
   };
};

export function InvitePage(props: Record<string, any>) @{
   const token = String((props?.["params"] ?? {} as InviteParams)?.token ?? "").trim();
   let statusTitle = track("Opening invite...");
   let statusLabel = track("Finding the shared activity.");
   let statusIcon = track("fas fa-link");
   let hasFailed = track(false);

   async function resolveInvite() {
      if (!token) {
         hasFailed.value = true;
         statusIcon.value = "fas fa-triangle-exclamation";
         statusTitle.value = "Invite missing";
         statusLabel.value = "This invite link does not include a token.";
         return;
      }

      try {
         const response = await fetch(`/api/invites/${encodeURIComponent(token)}`);
         if (!response.ok) {
            const message = await parseApiError(response, "Invite link could not be opened.");
            hasFailed.value = true;
            statusIcon.value = "fas fa-triangle-exclamation";
            statusTitle.value = "Invite unavailable";
            statusLabel.value = message;
            notyf.error(message);
            return;
         }

         const payload = await parseApiData<InviteResponse>(response);
         const activityId = Number(payload?.activity?.id);

         if (!Number.isFinite(activityId) || activityId <= 0) {
            hasFailed.value = true;
            statusIcon.value = "fas fa-triangle-exclamation";
            statusTitle.value = "Invite unavailable";
            statusLabel.value = "This invite does not point to a valid activity.";
            return;
         }

         navigateTo("/activities", {
            searchParams: {
               id: String(activityId),
               invite: token,
            },
         });
      } catch {
         hasFailed.value = true;
         statusIcon.value = "fas fa-wifi";
         statusTitle.value = "Could not open invite";
         statusLabel.value = "Please check your connection and try again.";
         notyf.error("Could not open invite.");
      }
   }

   function goToActivities() {
      navigateTo("/activities");
   }

   effect(() => {
      void resolveInvite();
   }, [token]);

   <>
   <section class="invite-page page-enter">
      <div class="invite-state">
         <StatusMessage icon={statusIcon.value} title={statusTitle.value} label={statusLabel.value} loading={!hasFailed.value ? "true" : undefined} />

         @if (hasFailed.value) {
            <button class="button white" type="button" onClick={goToActivities}>
               <i class="fas fa-calendar-days" />
               {"Browse activities"}
            </button>
         }
      </div>
   </section>

   <style scoped>
      .invite-page {
         display: flex;
         justify-content: center;
         align-items: center;
         min-height: 55vh;
      }

      .invite-state {
         display: flex;
         flex-direction: column;
         align-items: center;
         gap: 1rem;
      }

      .invite-state .button {
         width: fit-content;
      }
   </style>
   </>
}