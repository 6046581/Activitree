import { navigateTo } from "ripple-router";

import { StatusMessage } from "../views/StatusMessage.tsrx";

export function NotFoundPage() @{
   <>
   <article class="page-enter">
      <StatusMessage
         icon="fas fa-exclamation-triangle"
         title="Whoopsies! That's a 404..."
         label="The page you are looking for does not exist."
         link={{
            href: "/",
            text: "Take me Home",
            onClick: (e: MouseEvent) => {
               e.preventDefault();
               navigateTo("/");
            },
         }}
      />
   </article>
   </>
}