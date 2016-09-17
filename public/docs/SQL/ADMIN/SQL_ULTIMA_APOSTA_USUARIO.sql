/* DATA ULTIMA APOSTA USUARIO */
select	max(a.aposta_data) as data,
			u.usuario_nome,
			u.usuario_email			
from		aposta a
			inner join usuario u on a.usuario_id = u.usuario_id
group by u.usuario_id
order by max(a.aposta_data) desc