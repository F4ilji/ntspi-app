import{M as I}from"./MainNavbar-5-nqk6c_.js";import{i as k,r as n,o as l,c as r,b as e,a as i,w as c,p as _,j as v,t as g,l as p,Z as P,F as w,d as j,e as D}from"./app-lT3z3TR3.js";import{F}from"./v3-B5UP5GZw.js";import{C as q}from"./ClientScrollTimeline-BxA_EWwM.js";import{B as E,C as A}from"./SearchModal-YI3bdWA9.js";import{A as H,a as O,b as T}from"./AdminIndexHeaderTitle-DyC17FG6.js";import{A as $}from"./AdminIndexHeader-Ci_3kRvq.js";import{C as z,a as V}from"./ClientPostSearch-85XLLeIC.js";import{C as W}from"./ClientPost-KvELvIac.js";import{P as Y}from"./EventBadgeBuilder-Bg3wvoOe.js";import{M as Z}from"./MainPageNavbar-BfM50q-7.js";import{_ as y}from"./_plugin-vue_export-helper-DlAUqK2U.js";/* empty css                                                                   */import"./SortingByFilter-CjZ8gKSr.js";const G={name:"NewsBreadcrumbs",components:{BaseIcon:E,Link:k},data(){return{}},methods:{textLimit(s,t){if(s.length>t){let o;return o=s.substring(0,t),o+"..."}return s},isMobileDevice(){return window.innerWidth<1024},handleSectionClick(s){this.isMobileDevice()?this.toggleMobileNavSection(s):this.toggleDesktopNavSection(s)},handleSubSectionClick(s,t){this.isMobileDevice()?this.toggleMobileNavSubSection(s,t):this.toggleDesktopNavSubSection(s,t)},highlightNavItem(s){s.classList.add("animate-pulse"),setTimeout(()=>{s.classList.remove("animate-pulse")},4e3)},openMobileNavMenu(){document.getElementById("open-mobile-btn").click()},toggleMobileNavSubSection(s,t){this.openMobileNavMenu();const o=document.getElementById("open-mobile-nav"),d=o.querySelector("#nav-section-accordion-"+s.data.slug),h=o.querySelector("#nav-section-accordion-btn-"+s.data.slug);d.classList.contains("active")||h.click();const a=o.querySelector("#nav-sub-section-accordion-"+t.data.slug),m=o.querySelector("#nav-sub-section-accordion-btn-"+t.data.slug);a.classList.contains("active")&&m.click(),this.highlightNavItem(a)},toggleMobileNavSection(s){const t=document.getElementById("open-mobile-nav"),o=t.querySelector("#nav-section-accordion-"+s.data.slug),d=t.querySelector("#nav-section-accordion-btn-"+s.data.slug);o.classList.contains("active")&&d.click(),this.openMobileNavMenu(),this.highlightNavItem(o)},toggleDesktopNavSubSection(s,t){const o=document.getElementById("desktop-nav"),d=o.querySelector("#nav-sub-section-title-"+t.data.slug);o.querySelector("#nav-section-btn-"+s.data.slug).click(),this.highlightNavItem(d)},toggleDesktopNavSection(s){const t=document.getElementById("desktop-nav");t.querySelector("#nav-section-menu-"+s.data.slug),t.querySelector("#nav-section-btn-"+s.data.slug).click()}},props:{breadcrumbs:{type:Object},postTitle:{type:String}}},J={class:"flex justify-between pb-4 items-center"},K={class:"flex w-full sm:items-center gap-x-5 sm:gap-x-3"},Q={class:"grow"},R={class:"grid sm:flex sm:justify-between sm:items-center gap-2"},U={key:0,class:"flex items-center whitespace-normal min-w-0 flex-wrap gap-y-2","aria-label":"Breadcrumb"},X={class:"text-sm"},ee={key:0,class:"text-sm"},te={key:1,class:"text-sm"},se={class:"text-sm"},oe={key:1,class:"flex items-center whitespace-nowrap min-w-0 flex-wrap","aria-label":"Breadcrumb"},ie={class:"text-sm"},ne={class:"text-sm"};function ae(s,t,o,d,h,a){const m=n("BaseIcon"),u=n("Link");return l(),r("div",J,[e("div",K,[e("div",Q,[e("div",R,[o.breadcrumbs?(l(),r("ol",U,[e("li",X,[i(u,{href:s.route("index"),class:"flex items-center text-gray-500 hover:text-blue-600"},{default:c(()=>[i(m,{class:"size-5",name:"home"})]),_:1},8,["href"])]),o.breadcrumbs.mainSection?(l(),r("li",ee,[e("span",{class:"flex items-center text-gray-500 hover:text-primaryBlue cursor-pointer",onClick:t[0]||(t[0]=_(x=>a.handleSectionClick(this.breadcrumbs.mainSection),["prevent"]))},[t[2]||(t[2]=e("svg",{class:"flex-shrink-0 mx-2 overflow-visible h-2.5 w-2.5 text-gray-400",width:"16",height:"16",viewBox:"0 0 16 16",fill:"none",xmlns:"http://www.w3.org/2000/svg"},[e("path",{d:"M5 1L10.6869 7.16086C10.8637 7.35239 10.8637 7.64761 10.6869 7.83914L5 14",stroke:"currentColor","stroke-width":"2","stroke-linecap":"round"})],-1)),v(" "+g(a.textLimit(this.breadcrumbs.mainSection.data.title,25)),1)])])):p("",!0),o.breadcrumbs.subSection?(l(),r("li",te,[e("span",{class:"flex items-center text-gray-500 hover:text-primaryBlue cursor-pointer",onClick:t[1]||(t[1]=_(x=>a.handleSubSectionClick(this.breadcrumbs.mainSection,this.breadcrumbs.subSection),["prevent"]))},[t[3]||(t[3]=e("svg",{class:"flex-shrink-0 mx-2 overflow-visible h-2.5 w-2.5 text-gray-400",width:"16",height:"16",viewBox:"0 0 16 16",fill:"none",xmlns:"http://www.w3.org/2000/svg"},[e("path",{d:"M5 1L10.6869 7.16086C10.8637 7.35239 10.8637 7.64761 10.6869 7.83914L5 14",stroke:"currentColor","stroke-width":"2","stroke-linecap":"round"})],-1)),v(" "+g(a.textLimit(this.breadcrumbs.subSection.data.title,25)),1)])])):p("",!0),e("li",se,[i(u,{href:s.route("client.post.index"),class:"flex items-center text-gray-500 hover:text-blue-600"},{default:c(()=>[t[4]||(t[4]=e("svg",{class:"flex-shrink-0 mx-2 overflow-visible h-2.5 w-2.5 text-gray-400",width:"16",height:"16",viewBox:"0 0 16 16",fill:"none",xmlns:"http://www.w3.org/2000/svg"},[e("path",{d:"M5 1L10.6869 7.16086C10.8637 7.35239 10.8637 7.64761 10.6869 7.83914L5 14",stroke:"currentColor","stroke-width":"2","stroke-linecap":"round"})],-1)),v(" "+g(a.textLimit("Новости",30)),1)]),_:1},8,["href"])])])):p("",!0),o.breadcrumbs?p("",!0):(l(),r("ol",oe,[e("li",ie,[i(u,{href:s.route("index"),class:"flex items-center text-gray-500 hover:text-blue-600"},{default:c(()=>[i(m,{class:"size-5",name:"home"})]),_:1},8,["href"])]),e("li",ne,[i(u,{href:s.route("client.post.index"),class:"flex items-center text-gray-500 hover:text-blue-600"},{default:c(()=>[t[5]||(t[5]=e("svg",{class:"flex-shrink-0 mx-2 overflow-visible h-2.5 w-2.5 text-gray-400",width:"16",height:"16",viewBox:"0 0 16 16",fill:"none",xmlns:"http://www.w3.org/2000/svg"},[e("path",{d:"M5 1L10.6869 7.16086C10.8637 7.35239 10.8637 7.64761 10.6869 7.83914L5 14",stroke:"currentColor","stroke-width":"2","stroke-linecap":"round"})],-1)),v(" "+g(a.textLimit("Новости",30)),1)]),_:1},8,["href"])])]))])])])])}const le=y(G,[["render",ae]]),re={name:"Index",components:{NewsBreadcrumbs:le,MainPageNavBar:Z,PostBadge:Y,AdminIndexHeaderTitle:H,AdminIndexHeader:$,AdminIndexFilter:O,AdminIndexSearch:T,ClientFooterDown:A,ClientScrollTimeline:q,ClientPostFilter:z,Link:k,MainNavbar:I,FsLightbox:F,Head:P,ClientPost:W,ClientPostSearch:V},data(){return{}},props:{posts:{type:Object},filters:{type:Object},categories:{type:Object},tags:{type:Object},navigation:{type:Object},breadcrumbs:{type:Object}},methods:{},mounted(){}},ce={class:"flex flex-col h-screen"},de={class:"flex-grow"},me={class:"relative mx-auto mt-[67px] max-w-screen-xl py-10 md:flex md:flex-row md:py-10"},ue={class:"pt-6 lg:pt-10 pb-12 sm:px-6 lg:px-8 mx-auto"},ge={class:"px-6"},pe={key:0,class:"text-sm text-gray-500 mb-4"},he={class:"flex-wrap flex gap-3 md:items-center"},ve={class:"space-y-5 md:space-y-4"},xe={class:"space-y-5 md:space-y-4"},fe={class:"container px-4 mx-auto xl:px-5 max-w-screen-lg py-5 lg:py-8"},be={class:"mt-10 grid gap-10 md:grid-cols-2 lg:gap-10 xl:grid-cols-3"},_e={class:"mt-10 flex items-center justify-center"},we={class:"isolate inline-flex -space-x-px rounded-md shadow-sm","aria-label":"Pagination"};function ke(s,t,o,d,h,a){const m=n("Head"),u=n("MainPageNavBar"),x=n("NewsBreadcrumbs"),B=n("ClientPostSearch"),N=n("ClientPostFilter"),S=n("AdminIndexHeader"),C=n("PostBadge"),L=n("ClientPost"),f=n("Link"),M=n("ClientFooterDown");return l(),r(w,null,[i(m,null,{default:c(()=>t[0]||(t[0]=[e("title",null,"Новости",-1),e("meta",{name:"description",content:"Your page description"},null,-1)])),_:1}),i(u,{class:"border-b",sections:s.$page.props.navigation},null,8,["sections"]),e("div",ce,[e("main",de,[e("div",me,[e("div",ue,[t[3]||(t[3]=e("div",{class:"max-w-2xl text-center mx-auto mb-10 lg:mb-14"},[e("h2",{class:"text-2xl font-bold md:text-4xl md:leading-tight dark:text-white"},"Новости НТГСПИ"),e("p",{class:"mt-1 text-gray-600 dark:text-neutral-400"},"Узнайте последние новости любимого вуза")],-1)),i(x,{breadcrumbs:o.breadcrumbs,class:"px-5"},null,8,["breadcrumbs"]),e("div",null,[i(S,null,{default:c(()=>[i(B,{search_filter:this.filters.search_filter},null,8,["search_filter"]),i(N,{"sorting-by_filter":this.filters.sortingBy_filter,category_filter:this.filters.category_filter,tag_filter:this.filters.tag_filter,tags:o.tags,items:o.categories},null,8,["sorting-by_filter","category_filter","tag_filter","tags","items"])]),_:1}),e("div",ge,[o.filters.category_filter.value||o.filters.tag_filter.value||o.filters.search_filter.value?(l(),r("h3",pe,"Найдено новостей: "+g(o.posts.meta.total),1)):p("",!0),e("div",he,[i(C,{filters:this.filters},null,8,["filters"])])]),e("div",ve,[e("div",xe,[e("div",null,[e("div",fe,[e("div",be,[(l(!0),r(w,null,j(o.posts.data,b=>(l(),D(L,{key:b.id,post:b},null,8,["post"]))),128))]),e("div",_e,[e("nav",we,[i(f,{as:"button",href:o.posts.links.prev,disabled:s.$props.posts.links.prev===null,class:"relative inline-flex items-center gap-1 rounded-l-md border border-gray-300 bg-white px-3 py-2 pr-4 text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-20 disabled:pointer-events-none disabled:opacity-40 dark:border-gray-500 dark:bg-gray-800 dark:text-gray-300"},{default:c(()=>t[1]||(t[1]=[e("svg",{xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor","aria-hidden":"true","data-slot":"icon",class:"h-3 w-3"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M15.75 19.5 8.25 12l7.5-7.5"})],-1),e("span",null,"Предыдущая",-1)])),_:1},8,["href","disabled"]),i(f,{as:"button",href:o.posts.links.next,disabled:s.$props.posts.links.next===null,class:"relative inline-flex items-center gap-1 rounded-r-md border border-gray-300 bg-white px-3 py-2 pl-4 text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-20 disabled:pointer-events-none disabled:opacity-40 dark:border-gray-500 dark:bg-gray-800 dark:text-gray-300"},{default:c(()=>t[2]||(t[2]=[e("span",null,"Следующая",-1),e("svg",{xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor","aria-hidden":"true","data-slot":"icon",class:"h-3 w-3"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"m8.25 4.5 7.5 7.5-7.5 7.5"})],-1)])),_:1},8,["href","disabled"])])])])])])])])])])]),i(M)])],64)}const Ae=y(re,[["render",ke]]);export{Ae as default};