/* SQL ACESSO PERMANENCIA */
select	u.usuario_nome,
			sum(time_to_sec(timediff(l.log_acesso_logout, l.log_acesso_login))) / 3600 as 'tempo (h)'
from		log_acesso l
			inner join usuario u on l.usuario_id = u.usuario_id
where		l.log_acesso_logout is not null
group by u.usuario_id
order by sum(time_to_sec(timediff(l.log_acesso_logout, l.log_acesso_login))) desc