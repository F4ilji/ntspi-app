import{M as N}from"./MainNavbar-5-nqk6c_.js";import{i as k,r,o as a,c,b as e,a as i,w as u,p as b,j as v,t as h,l as p,Z as S,F as w,d as L,e as C}from"./app-lT3z3TR3.js";import{F as M}from"./v3-B5UP5GZw.js";import{C as I}from"./ClientScrollTimeline-BxA_EWwM.js";import{B as $,C as F}from"./SearchModal-YI3bdWA9.js";import{A as D,a as q,b as E}from"./AdminIndexHeaderTitle-DyC17FG6.js";import{A as T}from"./AdminIndexHeader-Ci_3kRvq.js";import{C as A,a as j}from"./ClientPostSearch-85XLLeIC.js";import{C as P}from"./ClientPost-KvELvIac.js";import{M as H}from"./MainPageNavbar-BfM50q-7.js";import{_}from"./_plugin-vue_export-helper-DlAUqK2U.js";/* empty css                                                                   */import"./SortingByFilter-CjZ8gKSr.js";const V={name:"FacultyListBreadcrumbs",components:{BaseIcon:$,Link:k},data(){return{}},methods:{textLimit(s,t){if(s.length>t){let o;return o=s.substring(0,t),o+"..."}return s},isMobileDevice(){return window.innerWidth<1024},handleSectionClick(s){this.isMobileDevice()?this.toggleMobileNavSection(s):this.toggleDesktopNavSection(s)},handleSubSectionClick(s,t){this.isMobileDevice()?this.toggleMobileNavSubSection(s,t):this.toggleDesktopNavSubSection(s,t)},highlightNavItem(s){s.classList.add("animate-pulse"),setTimeout(()=>{s.classList.remove("animate-pulse")},4e3)},openMobileNavMenu(){document.getElementById("open-mobile-btn").click()},toggleMobileNavSubSection(s,t){this.openMobileNavMenu();const o=document.getElementById("open-mobile-nav"),l=o.querySelector("#nav-section-accordion-"+s.data.slug),g=o.querySelector("#nav-section-accordion-btn-"+s.data.slug);l.classList.contains("active")||g.click();const n=o.querySelector("#nav-sub-section-accordion-"+t.data.slug),d=o.querySelector("#nav-sub-section-accordion-btn-"+t.data.slug);n.classList.contains("active")&&d.click(),this.highlightNavItem(n)},toggleMobileNavSection(s){const t=document.getElementById("open-mobile-nav"),o=t.querySelector("#nav-section-accordion-"+s.data.slug),l=t.querySelector("#nav-section-accordion-btn-"+s.data.slug);o.classList.contains("active")&&l.click(),this.openMobileNavMenu(),this.highlightNavItem(o)},toggleDesktopNavSubSection(s,t){const o=document.getElementById("desktop-nav"),l=o.querySelector("#nav-sub-section-title-"+t.data.slug);o.querySelector("#nav-section-btn-"+s.data.slug).click(),this.highlightNavItem(l)},toggleDesktopNavSection(s){const t=document.getElementById("desktop-nav");t.querySelector("#nav-section-menu-"+s.data.slug),t.querySelector("#nav-section-btn-"+s.data.slug).click()}},props:{breadcrumbs:{type:Object},eventTitle:{type:String}}},z={class:"flex justify-between pb-4 items-center"},W={class:"flex w-full sm:items-center gap-x-5 sm:gap-x-3"},O={class:"grow"},Y={class:"grid sm:flex sm:justify-between sm:items-center gap-2"},Z={key:0,class:"flex items-center whitespace-normal min-w-0 flex-wrap gap-y-2","aria-label":"Breadcrumb"},G={class:"text-sm"},J={key:0,class:"text-sm"},K={key:1,class:"text-sm"},Q={class:"text-sm"},R={key:1,class:"flex items-center whitespace-nowrap min-w-0 flex-wrap","aria-label":"Breadcrumb"},U={class:"text-sm"},X={class:"text-sm"};function ee(s,t,o,l,g,n){const d=r("BaseIcon"),m=r("Link");return a(),c("div",z,[e("div",W,[e("div",O,[e("div",Y,[o.breadcrumbs?(a(),c("ol",Z,[e("li",G,[i(m,{href:s.route("index"),class:"flex items-center text-gray-500 hover:text-blue-600"},{default:u(()=>[i(d,{class:"size-5",name:"home"})]),_:1},8,["href"])]),o.breadcrumbs.mainSection?(a(),c("li",J,[e("span",{class:"flex items-center text-gray-500 hover:text-primaryBlue cursor-pointer",onClick:t[0]||(t[0]=b(x=>n.handleSectionClick(this.breadcrumbs.mainSection),["prevent"]))},[t[2]||(t[2]=e("svg",{class:"flex-shrink-0 mx-2 overflow-visible h-2.5 w-2.5 text-gray-400",width:"16",height:"16",viewBox:"0 0 16 16",fill:"none",xmlns:"http://www.w3.org/2000/svg"},[e("path",{d:"M5 1L10.6869 7.16086C10.8637 7.35239 10.8637 7.64761 10.6869 7.83914L5 14",stroke:"currentColor","stroke-width":"2","stroke-linecap":"round"})],-1)),v(" "+h(n.textLimit(this.breadcrumbs.mainSection.data.title,25)),1)])])):p("",!0),o.breadcrumbs.subSection?(a(),c("li",K,[e("span",{class:"flex items-center text-gray-500 hover:text-primaryBlue cursor-pointer",onClick:t[1]||(t[1]=b(x=>n.handleSubSectionClick(this.breadcrumbs.mainSection,this.breadcrumbs.subSection),["prevent"]))},[t[3]||(t[3]=e("svg",{class:"flex-shrink-0 mx-2 overflow-visible h-2.5 w-2.5 text-gray-400",width:"16",height:"16",viewBox:"0 0 16 16",fill:"none",xmlns:"http://www.w3.org/2000/svg"},[e("path",{d:"M5 1L10.6869 7.16086C10.8637 7.35239 10.8637 7.64761 10.6869 7.83914L5 14",stroke:"currentColor","stroke-width":"2","stroke-linecap":"round"})],-1)),v(" "+h(n.textLimit(this.breadcrumbs.subSection.data.title,25)),1)])])):p("",!0),e("li",Q,[i(m,{href:s.route("client.faculty.index"),class:"flex items-center text-gray-500 hover:text-blue-600"},{default:u(()=>[t[4]||(t[4]=e("svg",{class:"flex-shrink-0 mx-2 overflow-visible h-2.5 w-2.5 text-gray-400",width:"16",height:"16",viewBox:"0 0 16 16",fill:"none",xmlns:"http://www.w3.org/2000/svg"},[e("path",{d:"M5 1L10.6869 7.16086C10.8637 7.35239 10.8637 7.64761 10.6869 7.83914L5 14",stroke:"currentColor","stroke-width":"2","stroke-linecap":"round"})],-1)),v(" "+h(n.textLimit("Факультеты",30)),1)]),_:1},8,["href"])])])):p("",!0),o.breadcrumbs?p("",!0):(a(),c("ol",R,[e("li",U,[i(m,{href:s.route("index"),class:"flex items-center text-gray-500 hover:text-blue-600"},{default:u(()=>[i(d,{class:"size-5",name:"home"})]),_:1},8,["href"])]),e("li",X,[i(m,{href:s.route("client.faculty.index"),class:"flex items-center text-gray-500 hover:text-blue-600"},{default:u(()=>[t[5]||(t[5]=e("svg",{class:"flex-shrink-0 mx-2 overflow-visible h-2.5 w-2.5 text-gray-400",width:"16",height:"16",viewBox:"0 0 16 16",fill:"none",xmlns:"http://www.w3.org/2000/svg"},[e("path",{d:"M5 1L10.6869 7.16086C10.8637 7.35239 10.8637 7.64761 10.6869 7.83914L5 14",stroke:"currentColor","stroke-width":"2","stroke-linecap":"round"})],-1)),v(" "+h(n.textLimit("Факультеты",30)),1)]),_:1},8,["href"])])]))])])])])}const te=_(V,[["render",ee]]),se={name:"Index",components:{FacultyListBreadcrumbs:te,MainPageNavBar:H,AdminIndexHeaderTitle:D,AdminIndexHeader:T,AdminIndexFilter:q,AdminIndexSearch:E,ClientFooterDown:F,ClientScrollTimeline:I,ClientPostFilter:A,Link:k,MainNavbar:N,FsLightbox:M,Head:S,ClientPost:P,ClientPostSearch:j},data(){return{}},props:{faculties:{type:Array}},methods:{},mounted(){}},oe={class:"flex flex-col h-screen"},ne={class:"flex-grow"},ie={class:"relative mx-auto mt-[67px] max-w-screen-xl py-10 md:flex md:flex-row md:py-10"},ae={class:"px-4 pt-6 lg:pt-10 pb-12 sm:px-6 lg:px-8 mx-auto"},le={class:"space-y-5 md:space-y-4"},re={class:"space-y-5 md:space-y-4"},ce={class:"max-w-[85rem] px-0 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto"},de={class:"grid sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-3 gap-3 sm:gap-6"},me={class:"p-4 md:p-5"},ue={class:"flex justify-between items-center gap-x-3"},he={class:"grow"},ge={class:"group-hover:text-blue-600 font-semibold text-gray-800"},ve={class:"text-sm text-gray-500"};function pe(s,t,o,l,g,n){const d=r("Head"),m=r("MainPageNavBar"),x=r("FacultyListBreadcrumbs"),y=r("Link"),B=r("ClientFooterDown");return a(),c(w,null,[i(d,null,{default:u(()=>t[0]||(t[0]=[e("title",null,"Факультеты",-1),e("meta",{name:"description",content:"Your page description"},null,-1)])),_:1}),i(m,{class:"border-b",sections:s.$page.props.navigation},null,8,["sections"]),e("div",oe,[e("main",ne,[e("div",ie,[e("div",ae,[e("div",null,[e("div",le,[e("div",re,[e("div",ce,[t[2]||(t[2]=e("div",{class:"max-w-2xl text-center mx-auto mb-10 lg:mb-14"},[e("h2",{class:"text-2xl font-bold md:text-4xl md:leading-tight dark:text-white"},"Факультеты и кафедры"),e("p",{class:"mt-1 text-gray-600 dark:text-neutral-400"},"We've helped some great companies brand, design and get to market.")],-1)),i(x),e("div",de,[(a(!0),c(w,null,L(o.faculties.data,f=>(a(),C(y,{href:s.route("client.faculty.show",f.slug),class:"group flex flex-col bg-white border shadow-sm rounded-xl hover:shadow-md focus:outline-none focus:shadow-md transition"},{default:u(()=>[e("div",me,[e("div",ue,[e("div",he,[e("h3",ge,h(f.shortTitle),1),e("p",ve,h(f.title),1)]),t[1]||(t[1]=e("div",null,[e("svg",{class:"shrink-0 size-5 text-gray-800",xmlns:"http://www.w3.org/2000/svg",width:"24",height:"24",viewBox:"0 0 24 24",fill:"none",stroke:"currentColor","stroke-width":"2","stroke-linecap":"round","stroke-linejoin":"round"},[e("path",{d:"m9 18 6-6-6-6"})])],-1))])])]),_:2},1032,["href"]))),256))])])])])])])])]),i(B)])],64)}const Ie=_(se,[["render",pe]]);export{Ie as default};