import{i as S,Z as N,r as g,c as n,a as f,w as x,b as t,n as c,T as C,j as p,t as i,F as l,d,o as a,p as T,l as D,e as y}from"./app-lT3z3TR3.js";import{F as L}from"./v3-B5UP5GZw.js";import{M as E}from"./MainNavbar-5-nqk6c_.js";import{C as B,s as j}from"./SearchModal-YI3bdWA9.js";import{M}from"./MainPageNavbar-BfM50q-7.js";import{_ as F}from"./_plugin-vue_export-helper-DlAUqK2U.js";/* empty css                                                                   */const P={name:"Show",data(){return{scrollTop:!1,currentNavSection:null}},props:{person:{type:Object}},components:{MainPageNavBar:M,ClientFooterDown:B,MainNavbar:E,Link:S,FsLightbox:L,Head:N},methods:{textLimit(o,e){if(o.length>e){let r;return r=o.substring(0,e),r+"..."}return o},openEditorImagesOnSlide:function(o){this.slide=o,this.toggler=!this.toggler},isSameRoute(o){if(this.$page.props.ziggy.location===o)return!0},generateSlug:function(o){return j(o,{lower:!0,strict:!0,locale:"ru"})},onScroll(o){window.top.scrollY>100?this.scrollTop=!0:this.scrollTop=!1},scrollToTop(){window.scrollTo(0,0)}},mounted(){window.addEventListener("scroll",this.onScroll),window.addEventListener("scroll",()=>{const o=document.querySelectorAll("h2"),e=document.querySelector("#visor");let r=null;const v=e.getBoundingClientRect();if(v.top>window.scrollY){this.currentNavSection=null,r=null;return}for(let h=0;h<o.length;h++){const u=o[h],m=u.getBoundingClientRect();if(m.top>=0&&m.bottom<=window.innerHeight&&m.bottom>=v.top&&m.top<=v.bottom){u!==r&&(this.currentNavSection=u.id,r=u);break}}})},beforeDestroy(){window.removeEventListener("scroll",this.handleScroll),window.removeEventListener("scroll",this.onScroll)},computed:{}},R={class:"flex flex-col h-screen"},z={class:"flex-grow"},H={class:"relative mb-auto mx-auto mt-[67px] max-w-screen-xl px-4 py-10 md:flex md:flex-row md:py-10"},V={class:"order-last hidden w-56 shrink-0 lg:block"},A={class:"sticky top-[110px] h-[calc(100vh-110px)]"},Y={class:"styled-scrollbar max-h-[70vh] space-y-1.5 overflow-y-auto py-2 text-sm"},q={class:"anchor-li"},I={class:"anchor-li"},O={class:"anchor-li"},Z={class:"anchor-li"},U={class:"anchor-li"},G={class:"anchor-li"},J={class:"anchor-li"},K={class:"anchor-li"},Q={class:"anchor-li"},W={class:"anchor-li"},X={class:"w-full min-w-0 mt-1 max-w-6xl px-1 md:px-6",style:{}},$={class:"w-full mx-auto sm:px-6 lg:px-8"},tt={class:"flex items-center gap-x-10 gap-y-4 flex-wrap"},et={class:""},st=["src"],rt={class:"grow"},at={class:"text-2xl font-medium text-gray-800 dark:text-neutral-200"},ot={class:""},nt={class:"mt-5 flex flex-col gap-y-3"},it={class:"flex items-center gap-x-2.5"},lt={class:"text-[13px] text-gray-500 underline hover:text-gray-800 hover:decoration-2 focus:outline-none focus:decoration-2 dark:text-neutral-500 dark:hover:text-neutral-400",href:"#"},dt={class:"flex items-center gap-x-2.5"},ct={class:"text-[13px] text-gray-500 underline hover:text-gray-800 hover:decoration-2 focus:outline-none focus:decoration-2 dark:text-neutral-500 dark:hover:text-neutral-400",href:"#"},ut={class:"mt-10 sm:mt-14"},mt={class:"grid grid-cols-1 sm:grid-cols-1 gap-3"},xt={class:"p-4 border border-gray-200 rounded-lg dark:border-neutral-700"},pt={class:"font-semibold text-sm text-gray-800 dark:text-neutral-200"},ht={class:"mt-8"},gt={class:"list-disc ms-6 mt-3 space-y-1.5"},ft={class:"ps-1 text-sm text-gray-600 dark:text-neutral-400"},vt={class:"mt-8"},_t={class:"list-disc ms-6 mt-3 space-y-1.5"},yt={class:"ps-1 text-sm text-gray-600 dark:text-neutral-400"},wt={class:"mt-8"},kt={class:"list-disc ms-6 mt-3 space-y-1.5"},bt={class:"ps-1 text-sm text-gray-600 dark:text-neutral-400"},St={class:"mt-8"},Nt={class:"list-disc ms-6 mt-3 space-y-1.5"},Ct={class:"ps-1 text-sm text-gray-600 dark:text-neutral-400"},Tt={class:"mt-8"},Dt={class:"grid grid-cols-1 sm:grid-cols-1 gap-3"},Lt={class:"mt-1 text-sm text-gray-600 dark:text-neutral-400"},Et={class:"mt-1 text-sm text-gray-600 dark:text-neutral-400"},Bt={class:"mt-8"},jt={class:"list-disc ms-6 mt-3 space-y-1.5"},Mt={class:"ps-1 text-sm text-gray-600 dark:text-neutral-400"},Ft={class:"mt-8"},Pt={class:"list-disc ms-6 mt-3 space-y-1.5"},Rt={class:"ps-1 text-sm text-gray-600 dark:text-neutral-400"},zt={class:"mt-8"},Ht={class:"list-disc ms-6 mt-3 space-y-1.5"},Vt={class:"ps-1 text-sm text-gray-600 dark:text-neutral-400"};function At(o,e,r,v,h,u){const m=g("Head"),w=g("MainPageNavBar"),_=g("Link"),k=g("ClientFooterDown"),b=g("FsLightbox");return a(),n(l,null,[f(m,null,{default:x(()=>[t("title",null,i(r.person.data.name),1),e[1]||(e[1]=t("meta",{name:"description",content:"Your page description"},null,-1))]),_:1}),t("div",R,[f(w,{class:"border-b",sections:o.$page.props.navigation},null,8,["sections"]),t("main",z,[t("div",H,[e[17]||(e[17]=t("div",{class:"w-full h-[67px] fixed pointer-events-none",id:"visor"},null,-1)),t("nav",V,[t("div",A,[e[3]||(e[3]=t("div",{class:"text-gray-1000 mb-2 text-md font-medium"},"На этой странице",-1)),t("ul",Y,[t("li",q,[t("a",{class:c([{"translate-x-2 text-[#135aae]":this.currentNavSection==="education","bg-transperant text-gray-600 hover:text-gray-900":this.currentNavSection!=="education"},"duration-150 block py-1 px-2 leading-[1.6] rounded-md"]),href:"#education"},"Образование ",2)]),t("li",I,[t("a",{class:c([{"translate-x-2 text-[#135aae]":this.currentNavSection==="awards","bg-transperant text-gray-600 hover:text-gray-900":this.currentNavSection!=="awards"},"duration-150 block py-1 px-2 leading-[1.6] rounded-md"]),href:"#awards"},"Награды",2)]),t("li",O,[t("a",{class:c([{"translate-x-2 text-[#135aae]":this.currentNavSection==="professionalRetraining","bg-transperant text-gray-600 hover:text-gray-900":this.currentNavSection!=="professionalRetraining"},"duration-150 block py-1 px-2 leading-[1.6] rounded-md"]),href:"#professionalRetraining"},"Профессиональная переподготовка ",2)]),t("li",Z,[t("a",{class:c([{"translate-x-2 text-[#135aae]":this.currentNavSection==="professionalDevelopment","bg-transperant text-gray-600 hover:text-gray-900":this.currentNavSection!=="professionalDevelopment"},"duration-150 block py-1 px-2 leading-[1.6] rounded-md"]),href:"#professionalDevelopment"},"Повышение квалификации ",2)]),t("li",U,[t("a",{class:c([{"translate-x-2 text-[#135aae]":this.currentNavSection==="professDisciplines","bg-transperant text-gray-600 hover:text-gray-900":this.currentNavSection!=="professDisciplines"},"duration-150 block py-1 px-2 leading-[1.6] rounded-md"]),href:"#professDisciplines"},"Преподаваемые дисциплины ",2)]),t("li",G,[t("a",{class:c([{"translate-x-2 text-[#135aae]":this.currentNavSection==="workExperience","bg-transperant text-gray-600 hover:text-gray-900":this.currentNavSection!=="workExperience"},"duration-150 block py-1 px-2 leading-[1.6] rounded-md"]),href:"#workExperience"},"Стаж работы ",2)]),t("li",J,[t("a",{class:c([{"translate-x-2 text-[#135aae]":this.currentNavSection==="attendedConferences","bg-transperant text-gray-600 hover:text-gray-900":this.currentNavSection!=="attendedConferences"},"duration-150 block py-1 px-2 leading-[1.6] rounded-md"]),href:"#attendedConferences"},"Участие в конференциях ",2)]),t("li",K,[t("a",{class:c([{"translate-x-2 text-[#135aae]":this.currentNavSection==="participationScienceProjects","bg-transperant text-gray-600 hover:text-gray-900":this.currentNavSection!=="participationScienceProjects"},"duration-150 block py-1 px-2 leading-[1.6] rounded-md"]),href:"#participationScienceProjects"},"Участие в научных проектах ",2)]),t("li",Q,[t("a",{class:c([{"translate-x-2 text-[#135aae]":this.currentNavSection==="publications","bg-transperant text-gray-600 hover:text-gray-900":this.currentNavSection!=="publications"},"duration-150 block py-1 px-2 leading-[1.6] rounded-md"]),href:"#publications"},"Публикации ",2)]),t("li",W,[t("a",{class:c([{"translate-x-2 text-[#135aae]":this.currentNavSection==="other","bg-transperant text-gray-600 hover:text-gray-900":this.currentNavSection!=="other"},"duration-150 block py-1 px-2 leading-[1.6] rounded-md"]),href:"#other"},"Другое ",2)]),f(C,{name:"fade"},{default:x(()=>[h.scrollTop?(a(),n("li",{key:0,class:"anchor-li flex items-center py-2 border-t",onClick:e[0]||(e[0]=T((...s)=>u.scrollToTop&&u.scrollToTop(...s),["prevent"]))},e[2]||(e[2]=[t("button",{class:"bg-transperant text-gray-600 cursor-pointer hover:text-gray-900 duration-300 block px-2 leading-[1.6] rounded-md"},"К началу",-1),t("svg",{xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor",class:"w-[17px] text-gray-600"},[t("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M15 11.25l-3-3m0 0l-3 3m3-3v7.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"})],-1)]))):D("",!0)]),_:1})])])]),t("article",X,[t("div",$,[t("div",tt,[t("div",et,[t("img",{class:"shrink-0 w-full md:w-[200px] md:h-[300px] rounded-xl object-cover",src:"/storage/"+r.person.data.details.photo,alt:"Avatar"},null,8,st),p(" "+i(),1)]),t("div",rt,[t("h1",at,i(r.person.data.name),1),t("div",ot,[(a(!0),n(l,null,d(r.person.data.divisions_works,s=>(a(),y(_,{href:o.route("client.division.show",s.slug),class:"text-sm text-blue-500 dark:text-neutral-400 mr-2"},{default:x(()=>[p(i(s.position),1)]),_:2},1032,["href"]))),256)),(a(!0),n(l,null,d(r.person.data.faculties_works,s=>(a(),y(_,{href:o.route("client.faculty.show",s.slug),class:"text-sm text-blue-500 dark:text-neutral-400 mr-2"},{default:x(()=>[p(i(s.position),1)]),_:2},1032,["href"]))),256)),(a(!0),n(l,null,d(r.person.data.departments_work,s=>(a(),y(_,{href:o.route("client.department.show",{facultySlug:s.faculty_slug,departmentSlug:s.slug}),class:"text-sm text-blue-500 dark:text-neutral-400 mr-2"},{default:x(()=>[p(i(s.position),1)]),_:2},1032,["href"]))),256)),(a(!0),n(l,null,d(r.person.data.departments_teach,s=>(a(),y(_,{href:o.route("client.department.show",{facultySlug:s.faculty_slug,departmentSlug:s.slug}),class:"text-sm text-blue-500 dark:text-neutral-400 mr-2"},{default:x(()=>[p(i(s.position),1)]),_:2},1032,["href"]))),256))]),p(" "+i(console.log(r.person))+" ",1),t("ul",nt,[t("li",it,[e[4]||(e[4]=t("svg",{xmlns:"http://www.w3.org/2000/svg",fill:"none",viewBox:"0 0 24 24","stroke-width":"1.5",stroke:"currentColor",class:"shrink-0 size-3.5"},[t("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"})],-1)),t("a",lt,i(r.person.data.details.contactPhone),1)]),t("li",dt,[e[5]||(e[5]=t("svg",{class:"shrink-0 size-3.5",xmlns:"http://www.w3.org/2000/svg",width:"24",height:"24",viewBox:"0 0 24 24",fill:"none",stroke:"currentColor","stroke-width":"2","stroke-linecap":"round","stroke-linejoin":"round"},[t("rect",{width:"20",height:"16",x:"2",y:"4",rx:"2"}),t("path",{d:"m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"})],-1)),t("a",ct,i(r.person.data.details.contactEmail),1)])])])]),t("div",ut,[e[8]||(e[8]=t("h2",{id:"education",class:"mb-3 font-medium text-gray-800 dark:text-neutral-200"}," Направление подготовки ",-1)),t("div",mt,[t("div",xt,[e[6]||(e[6]=t("h3",{class:"mb-1 text-xs text-gray-600 dark:text-neutral-400"}," 2012 - 2013 ",-1)),t("p",pt,i(r.person.data.details.education),1),e[7]||(e[7]=t("p",{class:"mt-1 text-sm text-gray-600 dark:text-neutral-400"}," The University of Manchester ",-1))])])]),t("div",ht,[e[9]||(e[9]=t("h2",{id:"awards",class:"mb-3 font-medium text-gray-800 dark:text-neutral-200"}," Награды ",-1)),t("ul",gt,[(a(!0),n(l,null,d(r.person.data.details.awards,s=>(a(),n("li",ft,i(s.item),1))),256))])]),t("div",vt,[e[10]||(e[10]=t("h2",{id:"professionalRetraining",class:"mb-3 font-medium text-gray-800 dark:text-neutral-200"}," Профессиональная переподготовка ",-1)),t("ul",_t,[(a(!0),n(l,null,d(r.person.data.details.professionalRetraining,s=>(a(),n("li",yt,i(s.item),1))),256))])]),t("div",wt,[e[11]||(e[11]=t("h2",{id:"professionalDevelopment",class:"mb-3 font-medium text-gray-800 dark:text-neutral-200"}," Повышение квалификации ",-1)),t("ul",kt,[(a(!0),n(l,null,d(r.person.data.details.professionalDevelopment,s=>(a(),n("li",bt,i(s.item),1))),256))])]),t("div",St,[e[12]||(e[12]=t("h2",{id:"professDisciplines",class:"mb-3 font-medium text-gray-800 dark:text-neutral-200"}," Преподаваемые дисциплины ",-1)),t("ul",Nt,[(a(!0),n(l,null,d(r.person.data.details.professDisciplines,s=>(a(),n("li",Ct,i(s.item),1))),256))])]),t("div",Tt,[e[13]||(e[13]=t("h2",{id:"workExperience",class:"mb-3 font-medium text-gray-800 dark:text-neutral-200"}," Стаж ",-1)),t("div",Dt,[t("p",Lt," Стаж работы: "+i(r.person.data.details.workExperience),1),t("p",Et," Стаж по специальности: "+i(r.person.data.details.workExperience),1)])]),t("div",Bt,[e[14]||(e[14]=t("h2",{id:"attendedConferences",class:"mb-3 font-medium text-gray-800 dark:text-neutral-200"}," Участие в конференциях ",-1)),t("ul",jt,[(a(!0),n(l,null,d(r.person.data.details.attendedConferences,s=>(a(),n("li",Mt,i(s.item),1))),256))])]),t("div",Ft,[e[15]||(e[15]=t("h2",{id:"participationScienceProjects",class:"mb-3 font-medium text-gray-800 dark:text-neutral-200"}," Участие в научных проектах ",-1)),t("ul",Pt,[(a(!0),n(l,null,d(r.person.data.details.participationScienceProjects,s=>(a(),n("li",Rt,i(s.item),1))),256))])]),t("div",zt,[e[16]||(e[16]=t("h2",{id:"publications",class:"mb-3 font-medium text-gray-800 dark:text-neutral-200"}," Публикации ",-1)),t("ul",Ht,[(a(!0),n(l,null,d(r.person.data.details.publications,s=>(a(),n("li",Vt,i(s.item),1))),256))])])])])])]),f(k)]),f(b,{class:"z-1000",slide:o.slide,toggler:o.toggler,sources:o.editorImages},null,8,["slide","toggler","sources"])],64)}const Jt=F(P,[["render",At]]);export{Jt as default};