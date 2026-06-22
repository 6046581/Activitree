import { effect } from "ripple";
import { Router, Route } from "ripple-router";

// Pages
import { HomePage } from "./pages/HomePage.tsrx";
import { ActivitiesPage } from "./pages/ActivitiesPage.tsrx";
import { ProfilePage } from "./pages/ProfilePage.tsrx";
import { LoginPage } from "./pages/LoginPage.tsrx";
import { InvitePage } from "./pages/InvitePage.tsrx";
import { NotFoundPage } from "./pages/NotFoundPage.tsrx";

// Components
import { NavBar } from "./components/NavBar.tsrx";
import { Wallpaper } from "./components/Wallpaper.tsrx";

export function App() @{
   effect(() => {
      let previousPathname = window.location.pathname;

      function handlePopState() {
         const currentPathname = window.location.pathname;

         // HACK: force a full reload when the route path changes to prevent many many many display issues. Ripple-router is such a pain.
         if (true && currentPathname !== previousPathname) {
            window.location.reload();
            return;
         }

         previousPathname = currentPathname;
      }

      window.addEventListener("popstate", handlePopState);
      return () => window.removeEventListener("popstate", handlePopState);
   }, []);

   <>
   <div class="app">
      <Wallpaper />
      <NavBar />

      <Router>
         <Route path="/" element={HomePage} />
         <Route path="/activities" element={ActivitiesPage} />
         <Route path="/activities/:mode" element={ActivitiesPage} />
         <Route path="/invite/:token" element={InvitePage} />
         <Route path="/profile" element={ProfilePage} />
         <Route path="/login" element={LoginPage} />
         <Route path="**" element={NotFoundPage} />
      </Router>
   </div>
   </>
}