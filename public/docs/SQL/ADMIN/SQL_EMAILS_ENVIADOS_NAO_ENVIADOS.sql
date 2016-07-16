select	e.email_enviado,
			count(*) 
from		email e
group by e.email_enviado