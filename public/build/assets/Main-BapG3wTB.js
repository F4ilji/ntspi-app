var P=Object.defineProperty;var D=(n,e,a)=>e in n?P(n,e,{enumerable:!0,configurable:!0,writable:!0,value:a}):n[e]=a;var s=(n,e,a)=>D(n,typeof e!="symbol"?e+"":e,a);import{i as k,o as i,c,b as t,F as x,d as _,n as I,t as o,B as N,l as E,Z as M,r as h,a as g,w as b,j as A,k as L,e as R}from"./app-lT3z3TR3.js";import{_ as f}from"./_plugin-vue_export-helper-DlAUqK2U.js";import{C as F}from"./SearchModal-YI3bdWA9.js";import{M as G}from"./MainPageNavbar-BfM50q-7.js";import{C as $}from"./ClientPost-KvELvIac.js";import j from"./PageResourceList-DR-wDUVY.js";import"./v3-B5UP5GZw.js";import"./FormBuilder-C25m8kmA.js";const B={name:"slider",components:{Link:k},props:{slidersCarousel:{type:Object}},data(){return{currentIndex:0,percentage:0,intervalId:null}},methods:{next(){this.currentIndex=(this.currentIndex+1)%this.slidersCarousel.data.length,this.resetTimer()},prev(){this.currentIndex=(this.currentIndex-1+this.slidersCarousel.data.length)%this.slidersCarousel.data.length,this.resetTimer()},progressStatus(){this.percentage>=100?(this.resetTimer(),this.next()):this.percentage++},startTimer(){this.intervalId=setInterval(this.progressStatus,60)},stopTimer(){clearInterval(this.intervalId)},resetTimer(){this.percentage=0,this.stopTimer(),this.startTimer()}},mounted(){this.$emit("slider-mounted",this.$refs.sliderRef),this.startTimer()}},H={ref:"sliderRef",class:"relative z-0 min-h-[calc(100vh)] items-center"},U={class:"absolute -z-10 h-full w-full before:absolute before:z-10 before:h-full before:w-full before:bg-black/30"},z=["src"],V={class:"text-brand-primary mb-3 mt-2 text-3xl font-semibold tracking-tight text-white lg:text-5xl lg:leading-tight"},Y={class:"mt-8 flex space-x-3 text-gray-500 mb-8"},J={class:"flex flex-col gap-3 md:flex-row md:items-center"},K={class:"flex gap-3"},Z={class:"text-gray-100 line-clamp-3"},Q={href:"/author/erika-oliver"},W=["href"],q={key:0,class:"mx-auto max-w-screen-md px-5"},X={class:"mt-8 text-gray-500 text-white absolute bottom-[100px]"},tt={class:""},et={class:"flex space-x-3 items-center justify-between"},st={class:"flex space-x-3 w-[100px] items-center font-semibold text-xl"},lt={class:"flex w-full h-1 bg-gray-200 rounded-full overflow-hidden dark:bg-neutral-700",role:"progressbar","aria-valuenow":"25","aria-valuemin":"0","aria-valuemax":"100"};function ot(n,e,a,v,d,m){return i(),c("div",H,[t("div",U,[(i(!0),c(x,null,_(a.slidersCarousel.data,(r,p)=>(i(),c("img",{alt:"Thumbnail",loading:"eager",decoding:"async","data-nimg":"fill",class:I(["object-cover brightness-[0.7] transition-opacity duration-1000",{"opacity-1":d.currentIndex===p,"opacity-0":d.currentIndex!==p}]),sizes:"100vw",key:p,src:"/storage/"+r.image,style:{position:"absolute",height:"100%",width:"100%",inset:"0px",color:"transparent"}},null,10,z))),128))]),(i(!0),c(x,null,_(a.slidersCarousel.data,(r,p)=>(i(),c("div",{class:I([{block:d.currentIndex===p,hidden:d.currentIndex!==p},"mx-auto max-w-screen-md px-5 pt-[150px] pb-0"]),key:p},[t("h1",V,o(r.title),1),t("div",Y,[t("div",J,[t("div",K,[t("p",Z,[t("a",Q,o(r.content),1)])])])]),t("a",{href:r.link,class:"py-3 px-4 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-white text-white hover:border-white/70 hover:text-white/70 disabled:opacity-50 disabled:pointer-events-none"},o(r.link_text),9,W)],2))),128)),a.slidersCarousel.data.length>=2?(i(),c("div",q,[t("div",X,[t("div",tt,[t("div",et,[t("button",{onClick:e[0]||(e[0]=(...r)=>m.prev&&m.prev(...r)),class:"bg-gray-200 w-8 h-8 hover:bg-gray-300 text-gray-800 font-bold rounded-full"},e[2]||(e[2]=[t("svg",{class:"w-4 h-4 mx-auto my-auto",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24",xmlns:"http://www.w3.org/2000/svg"},[t("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M15 19l-7-7 7-7"})],-1)])),t("div",st,[t("span",null,o(this.currentIndex+1),1),t("div",lt,[t("div",{class:"flex flex-col justify-center rounded-full overflow-hidden bg-blue-600 text-xs text-white text-center whitespace-nowrap transition duration-500 dark:bg-blue-500 my-slider-progress-bar",style:N({width:`${d.percentage}%`})},null,4)]),t("span",null,o(a.slidersCarousel.data.length),1)]),t("button",{onClick:e[1]||(e[1]=(...r)=>m.next&&m.next(...r)),class:"bg-gray-200 w-8 h-8 hover:bg-gray-300 text-gray-800 font-bold rounded-full"},e[3]||(e[3]=[t("svg",{class:"w-4 h-4 mx-auto my-auto",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24",xmlns:"http://www.w3.org/2000/svg"},[t("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M9 19l7-7-7-7"})],-1)]))])])])])):E("",!0)],512)}const rt=f(B,[["render",ot]]),it={name:"TestTimer",data(){return{percentage:0,intervalId:null}},methods:{progressStatus(){this.percentage>=100?this.resetTimer():this.percentage++},startTimer(){this.intervalId=setInterval(this.progressStatus,60)},stopTimer(){clearInterval(this.intervalId)},resetTimer(){this.percentage=0,this.stopTimer(),this.startTimer()}},mounted(){this.startTimer()}},nt={class:"flex w-full h-1 bg-gray-200 rounded-full overflow-hidden dark:bg-neutral-700",role:"progressbar","aria-valuenow":"25","aria-valuemin":"0","aria-valuemax":"100"};function at(n,e,a,v,d,m){return i(),c("div",nt,[t("div",{class:"flex flex-col justify-center rounded-full overflow-hidden bg-blue-600 text-xs text-white text-center whitespace-nowrap transition duration-500 dark:bg-blue-500",style:N({width:`${d.percentage}%`})},null,4)])}const dt=f(it,[["render",at]]),ct={name:"sliderSecond",components:{TestTimer:dt},data(){return{photos:[{url:"https://www.ntspi.ru/upload/iblock/d6b/z1xKGOXRrLE.jpg",title:"Студенты ФППО провели занятия для педклассов школ города",description:"«Учителем быть престижно!» Под таким девизом в течение трех дней (6, 7 и 10 июня) на факультете психолого-педагогического образования студенты группы Нт-203о ППП (психологический клуб «Форсайт») под руководством Марины Вячеславовны Манаковой проводили познавательные мероприятие для педагогических классов школ города (№№ 61, 44, 75/42)"},{url:"https://www.ntspi.ru/upload/iblock/eaf/IMG_8259.jpg",title:"Интеллектуальный турнир «Моя страна – моя Россия» для педагогических классов города!",description:"11 июня в читальном зале НТГСПИ на базе педагогических классов школ города (№№ 44, 75/42, 61) студенты СГФ под руководством декана Ирины Викторовны Даренской и заместителя декана Анны Саввишны Аникиной провели интеллектуальный турнир в соревновательном формате «Моя страна – моя Россия!»"},{url:"https://www.ntspi.ru/upload/iblock/ee6/DSC01893.JPG",title:"Филологи 20 лет спустя",description:"Спустя 20 лет на ФФМК встретились выпускники 2004 г. специальности «Русский язык и литература». Приятным и волнительным для филологов было возвращение на свой «третий этаж», ставший родным за пять лет обучения в вузе. Самый трогательный момент − встреча с дорогими преподавателями, которые за прошедшие годы совсем не изменились."},{url:"https://www.ntspi.ru/upload/iblock/f04/2.jpg",title:"Старт целины Штаба СО НТ – 2024",description:"7 мая в Городском дворце молодежи состоятся творческий старт сезона 2024 года студенческих отрядов города Нижний Тагил. Зажигательные, яркие, творческие бойцы отрядов показали свои выступления в преддверии старта сезона! Выступления отрядов, среди которых были и строительные, и педагогические, проводники и социальные отряды, были энергичными и фантастическими: торжественный выход с флагами отрядов, выступления по профилю работы, поразили выступления смешанных составов!"},{url:"https://www.ntspi.ru/upload/iblock/fc9/p-dIqCDymRY.jpg",title:"Студенты ФСБЖ – участники летней оздоровительной кампании – 2024",description:"3 июня студенты 3 курса факультета спорта и безопасности жизнедеятельности приняли участие в открытии лагеря с дневным пребыванием «Лето НТ» под девизом «Быть в движении!» Для 75 мальчишек и девчонок была подготовлена и проведена насыщенная интересными событиями программа. Открытие лагеря было посвящено Международному Дню защиты детей, где дети приняли участие в концертно-игровой программе «Планета детства»."}],currentIndex:0,percentage:0,intervalId:null}},methods:{next(){this.currentIndex=(this.currentIndex+1)%this.photos.length,this.resetTimer()},prev(){this.currentIndex=(this.currentIndex-1+this.photos.length)%this.photos.length,this.resetTimer()},progressStatus(){this.percentage>=100?(this.resetTimer(),this.next()):this.percentage++},startTimer(){this.intervalId=setInterval(this.progressStatus,60)},stopTimer(){clearInterval(this.intervalId)},resetTimer(){this.percentage=0,this.stopTimer(),this.startTimer()}},mounted(){this.startTimer()}},pt={class:"z-0 min-h-[calc(100vh-10vh)] items-center"},ut={class:"flex items-center pt-[150px] px-20"},mt={class:"text-brand-primary mb-3 mt-2 text-3xl font-semibold tracking-tight text-black lg:text-5xl lg:leading-tight"},xt={class:"mt-8 flex space-x-3 text-gray-500"},ht={class:"flex flex-col gap-3 md:flex-row md:items-center"},gt={class:"flex gap-3"},_t={class:"text-black"},bt={href:"/author/erika-oliver"},ft=["src"],vt={class:"mx-auto max-w-screen-md px-5"},wt={class:"mt-8 text-gray-500"},It={class:"flex space-x-3 items-center justify-between"},yt={class:"flex space-x-3 w-[100px] items-center font-semibold text-xl"},At={class:"flex w-full h-1 bg-gray-200 rounded-full overflow-hidden dark:bg-neutral-700",role:"progressbar","aria-valuenow":"25","aria-valuemin":"0","aria-valuemax":"100"},Rt={class:"space-x-2"};function Et(n,e,a,v,d,m){return i(),c("div",pt,[t("div",ut,[(i(!0),c(x,null,_(d.photos,(r,p)=>(i(),c("div",{class:I([{block:d.currentIndex===p,hidden:d.currentIndex!==p},"mx-auto max-w-screen-md px-5 pb-0"]),key:p},[t("h1",mt,o(r.title),1),t("div",xt,[t("div",ht,[t("div",gt,[t("p",_t,[t("a",bt,o(r.description),1)])])])])],2))),128)),(i(!0),c(x,null,_(d.photos,(r,p)=>(i(),c("img",{alt:"Thumbnail",loading:"eager",decoding:"async","data-nimg":"fill",class:I(["w-[500px] h-[500px] brightness-[0.7] object-cover transition-opacity duration-1000 rounded-full",{block:d.currentIndex===p,hidden:d.currentIndex!==p}]),sizes:"100vw",key:p,src:r.url,style:{color:"transparent"}},null,10,ft))),128))]),t("div",vt,[t("div",wt,[t("div",It,[t("div",yt,[t("span",null,o(this.currentIndex+1),1),t("div",At,[t("div",{class:"flex flex-col justify-center rounded-full overflow-hidden bg-blue-600 text-xs text-white text-center whitespace-nowrap transition duration-500 dark:bg-blue-500 my-slider-progress-bar",style:N({width:`${d.percentage}%`})},null,4)]),t("span",null,o(d.photos.length),1)]),t("div",Rt,[t("button",{onClick:e[0]||(e[0]=(...r)=>m.prev&&m.prev(...r)),class:"bg-gray-200 w-10 h-10 hover:bg-gray-300 text-gray-800 font-bold rounded-full"},e[2]||(e[2]=[t("svg",{class:"w-6 h-6 mx-auto my-auto",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24",xmlns:"http://www.w3.org/2000/svg"},[t("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M15 19l-7-7 7-7"})],-1)])),t("button",{onClick:e[1]||(e[1]=(...r)=>m.next&&m.next(...r)),class:"bg-gray-200 w-10 h-10 hover:bg-gray-300 text-gray-800 font-bold rounded-full"},e[3]||(e[3]=[t("svg",{class:"w-6 h-6 mx-auto my-auto",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24",xmlns:"http://www.w3.org/2000/svg"},[t("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M9 19l7-7-7-7"})],-1)]))])])])])])}const Nt=f(ct,[["render",Et]]);class l{static fromName(e){return this[e]||null}getName(){return this.name}}s(l,"PREPARATION_OF_QUALIFIED_WORKERS",{value:1,label:"Подготовка квалифицированных рабочих, служащих",color:"info",name:"PREPARATION_OF_QUALIFIED_WORKERS",type_label:"Среднее образование"}),s(l,"MIDDLE_LEVEL_SPECIALIST_TRAINING",{value:2,label:"Среднее профессиональное образование",color:"primary",name:"MIDDLE_LEVEL_SPECIALIST_TRAINING",type_label:"Среднее образование"}),s(l,"BACHELOR",{value:3,label:"Бакалавриат",color:"success",name:"BACHELOR",type_label:"Высшее образование"}),s(l,"MASTER",{value:4,label:"Магистратура",color:"success",name:"MASTER",type_label:"Высшее образование"}),s(l,"SPECIALIST",{value:5,label:"Специалитет",color:"warning",name:"SPECIALIST",type_label:"Высшее образование"}),s(l,"POSTGRADUATE",{value:6,label:"Аспирантура",color:"warning",name:"POSTGRADUATE",type_label:"Высшее образование"}),s(l,"ADJUNCTURE",{value:7,label:"Адъюнктура",color:"secondary",name:"ADJUNCTURE"}),s(l,"RESIDENCY",{value:8,label:"Ординатура",color:"secondary",name:"RESIDENCY"}),s(l,"INTERNSHIP",{value:9,label:"Ассистентура - стажировка",color:"light",name:"INTERNSHIP"}),s(l,"PROFESSIONAL_TRAINING",{value:10,label:"Профессиональная подготовка по профессиям рабочих, должностям служащих",color:"info",name:"PROFESSIONAL_TRAINING"}),s(l,"RETRAINING",{value:11,label:"Переподготовка рабочих, служащих",color:"danger",name:"RETRAINING"}),s(l,"ADVANCED_TRAINING",{value:12,label:"Повышение квалификации рабочих, служащих",color:"success",name:"ADVANCED_TRAINING"}),s(l,"ADDITIONAL_GENERAL_DEVELOPMENT_PROGRAM",{value:13,label:"Дополнительная общеразвивающая программа",color:"info",name:"ADDITIONAL_GENERAL_DEVELOPMENT_PROGRAM"}),s(l,"ADDITIONAL_PREPROFESSIONAL_PROGRAM",{value:14,label:"Дополнительная предпрофессиональная программа",color:"info",name:"ADDITIONAL_PREPROFESSIONAL_PROGRAM"}),s(l,"ADDITIONAL_PREPROFESSIONAL_ART_PROGRAM",{value:15,label:"Дополнительная предпрофессиональная программа в сфере искусств",color:"info",name:"ADDITIONAL_PREPROFESSIONAL_ART_PROGRAM"}),s(l,"PROFESSIONAL_ADVANCEMENT",{value:16,label:"Повышение квалификации",color:"success",name:"PROFESSIONAL_ADVANCEMENT"}),s(l,"PROFESSIONAL_RETRAINING",{value:17,label:"Профессиональная переподготовка",color:"danger",name:"PROFESSIONAL_RETRAINING"}),s(l,"PRESCHOOL_EDUCATION",{value:18,label:"Дошкольное образование",color:"success",name:"PRESCHOOL_EDUCATION"}),s(l,"PRIMARY_GENERAL_EDUCATION",{value:19,label:"Начальное общее образование",color:"success",name:"PRIMARY_GENERAL_EDUCATION"}),s(l,"BASIC_GENERAL_EDUCATION",{value:20,label:"Основное общее образование",color:"success",name:"BASIC_GENERAL_EDUCATION"}),s(l,"SECONDARY_GENERAL_EDUCATION",{value:21,label:"Среднее общее образование",color:"success",name:"SECONDARY_GENERAL_EDUCATION"}),s(l,"INTERNSHIP_PROGRAM",{value:22,label:"Интернатура",color:"light",name:"INTERNSHIP_PROGRAM"}),s(l,"ADDITIONAL_PREPROFESSIONAL_SPORT_PROGRAM",{value:23,label:"Дополнительная предпрофессиональная программа в сфере физической культуры и спорта",color:"info",name:"ADDITIONAL_PREPROFESSIONAL_SPORT_PROGRAM"}),s(l,"BASIC_HIGHER_EDUCATION",{value:24,label:"Базовое высшее образование",color:"success",name:"BASIC_HIGHER_EDUCATION"}),s(l,"SPECIALIZED_HIGHER_EDUCATION",{value:25,label:"Специализированное высшее образование",color:"success",name:"SPECIALIZED_HIGHER_EDUCATION"});const kt={name:"BaseMetaHead",data(){return{}},methods:{},props:{title:{type:String},description:{type:String},robots:{type:Array},og_title:{type:String},og_description:{type:String},og_image:{type:String}}};function Tt(n,e,a,v,d,m){return null}const St=f(kt,[["render",Tt]]),Ot={name:"Main",computed:{LevelEducational(){return l}},data(){return{sliderRef:null}},props:{posts:{type:Object},events:{type:Object},sliders:{type:Object},educations:{type:Object},icons:{type:String}},components:{PageResourceList:j,BaseMetaHead:St,ClientPost:$,MainPageNavBar:G,ClientFooterDown:F,ClientMainSlider:rt,ClientMainSliderSecond:Nt,Head:M,Link:k},methods:{setSliderRef(n){this.sliderRef=n}}},Ct={class:"max-w-screen-xl w-full mx-auto px-4 py-3 pb-10"},Pt={class:"grid gap-10 pb-10 md:grid-cols-2 lg:gap-10 xl:grid-cols-3"},Dt={class:"flex justify-center"},Mt=["href"],Lt={class:"max-w-[85rem] pb-10 sm:px-6 lg:px-8 lg:pb-14 mx-auto"},Ft={class:"grid sm:grid-cols-2 lg:grid-cols-3 gap-6"},Gt={class:"aspect-w-16 aspect-h-10"},$t={class:"w-full h-[250px] object-cover rounded-xl bg-[#F8F9FB]"},jt={class:"flex flex-col px-5 py-5 w-full h-full"},Bt={class:"flex flex-col gap-3 mb-4"},Ht={class:"gap-3 flex items-center"},Ut={class:"shadow-sm"},zt={class:"block w-[35px] h-[27px] bg-white rounded-b text-center font-medium"},Vt={class:"block first-letter:uppercase"},Yt={class:"flex"},Jt={class:"bg-[#E9F2FE] text-sm text-blue-600 px-2 py-1 rounded block"},Kt={class:"flex gap-2"},Zt={key:0,class:"bg-[#E9F2FE] text-sm text-blue-600 px-2 py-1 rounded block"},Qt={key:1},Wt={class:"bg-[#E9F2FE] text-sm text-blue-600 px-2 py-1 rounded block"},qt={class:"mt-5 text-xl text-gray-800"},Xt={class:"flex justify-center"},te={class:"w-full mx-auto"},ee={class:"grid sm:grid-cols-2 lg:grid-cols-2 gap-6"},se={class:"group flex flex-col h-full bg-white hover:opacity-70 hover:border-secondDarkBlue duration-300 border border-gray-200 shadow-sm rounded-xl"},le={class:"p-4 md:p-6"},oe={class:"block mb-1 text-xs font-semibold uppercase text-blue-600"},re={class:"text-3xl font-semibold text-gray-800"},ie={class:"mt-auto p-4 md:p-6 grid grid-cols-2 lg:grid-cols-3 sm:space-y-0"},ne={class:"pb-4"},ae={class:"text-2xl font-semibold text-blue-600"},de={class:"pb-4"},ce={class:"text-2xl font-semibold text-blue-600"},pe={class:"pb-4"},ue={class:"text-2xl font-semibold text-blue-600"},me={class:"pb-4"},xe={class:"text-2xl font-semibold text-blue-600"},he={class:"pb-4"},ge={class:"text-2xl font-semibold text-blue-600"},_e={class:"group flex flex-col h-full bg-white hover:opacity-70 hover:border-secondDarkBlue duration-300 border border-gray-200 shadow-sm rounded-xl"},be={class:"mt-auto p-4 md:p-6 grid grid-cols-2 lg:grid-cols-3 sm:space-y-0 space-y-3"},fe={class:"pb-4"},ve={class:"text-2xl font-semibold text-blue-600"},we={class:"pb-4"},Ie={class:"text-2xl font-semibold text-blue-600"};function ye(n,e,a,v,d,m){const r=h("Head"),p=h("MainPageNavBar"),T=h("ClientMainSlider"),S=h("ClientPost"),w=h("Link"),O=h("PageResourceList"),C=h("ClientFooterDown");return i(),c(x,null,[g(r,null,{default:b(()=>e[0]||(e[0]=[t("title",null,"Главная",-1),t("meta",{name:"description",content:"Your page description"},null,-1),t("meta",{name:"robots",content:"index, follow"},null,-1),t("meta",{property:"og:title",content:"Заголовок страницы"},null,-1),t("meta",{property:"og:description",content:"Описание страницы"},null,-1),t("meta",{property:"og:image",content:"URL_изображения"},null,-1)])),_:1}),g(p,{sections:n.$page.props.navigation,"slider-ref":d.sliderRef},null,8,["sections","slider-ref"]),g(T,{onSliderMounted:m.setSliderRef,slidersCarousel:a.sliders},null,8,["onSliderMounted","slidersCarousel"]),t("section",Ct,[e[14]||(e[14]=t("h2",{class:"text-brand-primary my-6 md:mb-[50px] md:mt-[80px] text-2xl font-semibold tracking-tight text-black lg:text-[32px] lg:leading-tight"},"Последние новости",-1)),t("div",Pt,[(i(!0),c(x,null,_(a.posts.data,u=>(i(),R(S,{key:u.id,post:u},null,8,["post"]))),128))]),t("div",Dt,[t("a",{href:n.route("client.post.index"),class:"group mt-3 inline-flex items-center gap-x-1 text-sm font-semibold text-primaryBlue"},e[1]||(e[1]=[A(" Все новости "),t("svg",{class:"flex-shrink-0 size-4 transition ease-in-out group-hover:translate-x-1",xmlns:"http://www.w3.org/2000/svg",width:"24",height:"24",viewBox:"0 0 24 24",fill:"none",stroke:"currentColor","stroke-width":"2","stroke-linecap":"round","stroke-linejoin":"round"},[t("path",{d:"m9 18 6-6-6-6"})],-1)]),8,Mt)]),e[15]||(e[15]=t("h2",{class:"text-brand-primary my-6 md:mb-[50px] md:mt-[80px] text-2xl font-semibold tracking-tight text-black lg:text-[32px] lg:leading-tight"},"Мероприятия",-1)),t("div",Lt,[t("div",Ft,[(i(!0),c(x,null,_(a.events.data,u=>(i(),R(w,{class:"group hover:bg-gray-100 rounded-xl p-5 transition-all",href:n.route("client.event.show",u.slug)},{default:b(()=>[t("div",Gt,[t("div",$t,[t("div",jt,[t("div",Bt,[t("div",Ht,[t("div",Ut,[e[2]||(e[2]=t("div",{class:"block w-[35px] h-[8px] bg-red-400 rounded-t"},null,-1)),t("div",zt,o(u.event_date_start.day),1)]),t("span",Vt,o(u.event_date_start.month),1),t("div",Yt,[t("span",Jt,"Начало - "+o(u.event_date_start.time),1)])])]),t("div",Kt,[u.is_online===1?(i(),c("span",Zt,"Онлайн")):E("",!0),u.is_online===0?(i(),c("div",Qt,[t("span",Wt,o(u.address),1)])):E("",!0)])])])]),t("h3",qt,o(u.title),1),e[3]||(e[3]=t("p",{class:"mt-3 inline-flex items-center gap-x-1 text-sm font-semibold text-gray-800"},[A(" Перейти "),t("svg",{class:"flex-shrink-0 size-4 transition ease-in-out group-hover:translate-x-1",xmlns:"http://www.w3.org/2000/svg",width:"24",height:"24",viewBox:"0 0 24 24",fill:"none",stroke:"currentColor","stroke-width":"2","stroke-linecap":"round","stroke-linejoin":"round"},[t("path",{d:"m9 18 6-6-6-6"})])],-1))]),_:2},1032,["href"]))),256))])]),t("div",Xt,[g(w,{href:n.route("client.event.index"),class:"group mt-3 inline-flex items-center gap-x-1 text-sm font-semibold text-[#1A5AAF]"},{default:b(()=>e[4]||(e[4]=[A(" Все мероприятия "),t("svg",{class:"flex-shrink-0 size-4 transition ease-in-out group-hover:translate-x-1",xmlns:"http://www.w3.org/2000/svg",width:"24",height:"24",viewBox:"0 0 24 24",fill:"none",stroke:"currentColor","stroke-width":"2","stroke-linecap":"round","stroke-linejoin":"round"},[t("path",{d:"m9 18 6-6-6-6"})],-1)])),_:1},8,["href"])]),e[16]||(e[16]=t("h2",{class:"text-brand-primary my-10 md:mb-[50px] md:mt-[80px] text-2xl font-semibold tracking-tight text-black lg:text-[32px] lg:leading-tight"},"Образование",-1)),t("div",te,[t("div",ee,[(i(!0),c(x,null,_(a.educations.admission_campaign,(u,y)=>(i(),R(w,{href:n.route("client.program.index",{level:m.LevelEducational[y].name})},{default:b(()=>[t("div",se,[t("div",le,[t("span",oe,o(m.LevelEducational[y].type_label),1),t("h3",re,o(m.LevelEducational[y].label),1),e[5]||(e[5]=t("p",{class:"mt-3 text-gray-500"}," Построй свою индивидуальную траекторию ",-1))]),t("div",ie,[t("div",ne,[t("p",ae,o(u.total_programs),1),e[6]||(e[6]=t("p",{class:"mt-1 text-sm text-gray-500"},"Программ",-1))]),t("div",de,[t("p",ce,o(u.places.och_count),1),e[7]||(e[7]=t("p",{class:"mt-1 text-sm text-gray-500"},"Очных",-1))]),t("div",pe,[t("p",ue,o(u.places.zaoch_count),1),e[8]||(e[8]=t("p",{class:"mt-1 text-sm text-gray-500"},"Заочных",-1))]),t("div",me,[t("p",xe,o(u.places.budget_places),1),e[9]||(e[9]=t("p",{class:"mt-1 text-sm text-gray-500"},"Бюджетных",-1))]),t("div",he,[t("p",ge,o(u.places.non_budget_places),1),e[10]||(e[10]=t("p",{class:"mt-1 text-sm text-gray-500"},"Платных",-1))])])])]),_:2},1032,["href"]))),256)),g(w,{href:n.route("client.additionalEducation.index")},{default:b(()=>[t("div",_e,[e[13]||(e[13]=t("div",{class:"p-4 md:p-6"},[t("span",{class:"block mb-1 text-xs font-semibold uppercase text-blue-600"}," Доп. образование "),t("h3",{class:"text-3xl font-semibold text-gray-800"}," Дополнительное образование "),t("p",{class:"mt-3 text-gray-500"}," Построй свою индивидуальную траекторию ")],-1)),t("div",be,[t("div",fe,[t("p",ve,o(a.educations.additional_education.educations_count),1),e[11]||(e[11]=t("p",{class:"mt-1 text-sm text-gray-500"},"Программ",-1))]),t("div",we,[t("p",Ie,o(a.educations.additional_education.categories_count),1),e[12]||(e[12]=t("p",{class:"mt-1 text-sm text-gray-500"},"Направлений",-1))])])])]),_:1},8,["href"])])]),g(O,{"resource-id":"glavnaia-stranica-resurs"})]),e[17]||(e[17]=L('<section class="bg-[#F5F5F5] w-full py-10"><div class="max-w-screen-xl md:flex justify-around w-full mx-auto px-4 py-[50px] flex-wrap"><div class="mb-[50px] space-y-3"><h2 class="font-semibold text-[#1A5AAF] text-lg">Контакты</h2><div><h3 class="font-semibold mb-4">Главный корпус</h3><div class="font-light"><p>622031, Нижний Тагил, Красногвардейская 57</p></div></div><div><h3 class="font-semibold mb-4">Свяжитесь с нами</h3><div class="flex gap-x-3 font-light"><p>+7(906)-802-55-59</p><p>ntgspi@yandex.ru</p></div></div></div><div class="mb-[50px] space-y-3"><h2 class="font-semibold text-[#1A5AAF] text-lg">Приемная комиссия</h2><div><h3 class="font-semibold mb-4">Расписание</h3><div class="font-light"><p>Понедельник - Пятница с 08.30 до 17.00</p></div></div><div><h3 class="font-semibold mb-4">Ответственный секретарь приемной комиссии</h3><div class="font-light"><p>+7(906)-802-55-59</p><p>ntgspi@yandex.ru</p></div></div></div><div class="space-y-3"><h2 class="font-semibold text-[#1A5AAF] text-lg">Полезное</h2><div><h3 class="font-semibold mb-4">Главный корпус</h3><div class="font-light"><p>Понедельник - Пятница <br> с 08.30 до 17.00</p></div></div><div><h3 class="font-semibold mb-4">Ответственный секретарь <br> приемной комиссии</h3><div class="font-light"><p>+7(906)-802-55-59</p><p>ntgspi@yandex.ru</p></div></div></div></div></section>',1)),g(C)],64)}const Pe=f(Ot,[["render",ye]]);export{Pe as default};