page.includeCSS {
	formhandler = fileadmin/templates/css/formhandler.css
}

page.includeJS {
	formhandler = fileadmin/templates/javascript/formhandler.js
}

plugin.Tx_Formhandler.settings.predef.contactform {
	name = Contact Form
	templateFile = typo3conf/ext/formhandler/ewform_contact/template.html
	langFile = typo3conf/ext/formhandler/ewform_contact/lang.xml
	#formValuesPrefix = formhandler
	
	isErrorMarker {
		global = TEXT
		global.data = LLL:EXT:formhandler/ewform_contact/lang.xml:error_global
	}
	
	preProcessors {
		1.class = Tx_Formhandler_PreProcessor_LoadDefaultValues
		1.config {
			1 {
				check_message.defaultValue = yes
				newsletter.defaultValue = yes
			}
		}
	}
	
	validators {
		1.class = Tx_Formhandler_Validator_Default
		1.config {
			fieldConf {
				name.errorCheck.1 = required
				email.errorCheck.1 = required
				email.errorCheck.2 = email
			}
		}
	}
	
	finishers.1.class = Finisher_Mail
	finishers.1.config {
	}
	finishers.3.class = Finisher_SubmittedOK
	finishers.3.config {
		returns = 1
	}
}

[globalString = GP:check_message=yes]
	plugin.Tx_Formhandler.settings.predef.contactform.validators.1.config.fieldConf.message.errorCheck.1 = required	
[global]

[globalString = GP:newsletter=yes]
	plugin.Tx_Formhandler.settings.predef.contactform {
		finishers.2.class = Finisher_DB
		finishers.2.config {
			table = fe_users
			fields {
				username.mapping = name
				name.mapping = name
				email.mapping = email
				tstamp.special = sub_tstamp
				crdate.special = sub_tstamp
				usergroup = 2
				pid = 16
			}
		}
	}
[global]
