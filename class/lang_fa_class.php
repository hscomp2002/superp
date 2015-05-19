<?php 
	class lang_fa_class
	{
		const	access_deny ='<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body><h2 align="center" >شما به این صفحه دسترسی ندارید</h2></body></html>';
		const	accessdeny = "نشست شما منقضی شده است دوباره وارد شوید";
		const	title = "SuperParvaz";
		const   grp_customer="مدیریت‌گروه‌های‌کاربری";
		const	all_manegment = "صفحه‌اصلی";
		const	welcom = "خوش آمدید";
		const	grp_user ="گزارش‌فروش";
		const	user = "مدیریت‌کاربران";
		const	time = "گزارش‌روزانه";
		const	bandwidth = "گزارش‌روزانه";
		const	filter = "انجام‌رزرو";
		const	download = "گزارشات‌دفاتر";
		const	logout = "خروج";
		const	successdel = "حذف با موفقیت انجام شد";
		const	unsuccessdel = "این مشخصه حداقل برای یک گروه اختصاص یافته است حذف ممکن نیست";
		const	successadd = "با موفقیت افزوده شد";
		const	success_done = "با موفقیت انجام شد";
		const 	unsuccess_done = "انجام عملیات نا موفق بود";
		const	unsuccessadd = "این مشخصه حداقل برای یک گروه اختصاص یافته است افزودن ممکن نیست";
		const	filter_name = "نام فیلتر";
		const	ask_del = "آیا حذف انجام شود؟";
		const	delete = "حذف";
		const	delete_conf = "آیا حذف انجام شود؟";
		const	edit = "ویرایش";
		const	new_item = "جدید";
		const	auth_users = "کاربران‌مجاز";
		const	save = "ذخیره ";
		const 	savechanges = "ذخیره‌تغییرات";
		const 	savenew = "ذخیره‌جدید";
		const	error_ip = "نشانی آی‌پی درست وارد نشده است";
		const	error_mac = "نشانی مک درست وارد نشده است";
		const	active = "وضعیت فعال";
		const	inactive = "وضعیت غیر‌فعال";
		const	grp = "گروه‌کاربری";
		const	lname = "نام‌خانوادگی";
		const	search = "جستجو";
		const	apply = "اعمال‌تغییرات";
		const	change_state_confirm ="آیا تغییر وضعیت پراکسی سرور انجام شود؟";
		const   grooh ="گروه‌حساب‌ها";
		const   customers ="مدیریت‌مشتریان";
		const   moeen ="حساب‌معین";
		const	shahr="مدیریت‌شهر";
		const   tafzili1 ="حساب تفضیلی1";
		const   tafzili2 ="حساب تفضیلی2";
		const   shenavar1 ="حساب تفضیلی شناور1";
		const   shenavar2 ="حساب تفضیلی شناور2";
		const   backup ="پشتیبان‌گیری";
		const	hotel = "هتل";
		const	kol = "هتل";
		const	sherkat_parvaz = "شرکتهای‌هواپیمایی";
		const	havapeima = "نام‌هواپیما";
		const	parvaz_manage="مدیریت‌پرواز";
		public function filterAlert($oks,$noks)
		{
			if($oks!="" && $noks!="" )
			{
				$oks = substr($oks,0,-1);
				$noks = substr($noks,0,-1);
				$out = "فیلتر(های)۰ \\n $oks \\n با موفقیت حذف شدند و فیلتر(های)۰ \\n $noks \\n حذف نشدند";
			}
			else if($oks!="")
			{
				$oks = substr($oks,0,-1);
				$out = "فیلتر(های)۰ \\n $oks \\n با موفقیت حذف شدند";
			}
			else if($noks!="")
                        {
                                $noks = substr($noks,0,-1);
                                $out = "فیلتر(های)۰ \\n $noks \\n حذف نشدند";
                        }
			else
			{
				$out = "خطا در عمالکرد";
			}
			return $out;
		}
                
	}
?>
