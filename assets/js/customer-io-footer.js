function waitForPianoUserData() {
	return new Promise((resolve) => {
		// Check if already ready
		if (window.PianoUserData && window.PianoUserData.isReady) {
			resolve(window.PianoUserData);
			return;
		}

		const interval = setInterval(() => {
			if (window.PianoUserData && window.PianoUserData.isReady === true) {
				clearInterval(interval);
				resolve(window.PianoUserData);
			}
		}, 50);
	});
}

(async () => {
	const data = await waitForPianoUserData();
	const userEmail = data.User_email;
	const user_status = data.User_status;
	const piano_user_id = data.User_id;
	
	if (user_status == 'registered' || user_status == 'subscriber') {
		cioanalytics.identify(userEmail, {
			email: userEmail,
		});
	}
	
	const pageMetadata = window.customerIOPageMetadata || {};
	cioanalytics.page(null, null, {
		piano_user_id: piano_user_id,
		user_status: user_status,
		edition: pageMetadata.edition || '',
		article_author: pageMetadata.article_author || '',
		article_section: pageMetadata.article_section || '',
		article_section_2: pageMetadata.article_section_2 || '',
		article_tags: pageMetadata.article_tags || [],
		article_topics: pageMetadata.article_topics || [],
	});
})();