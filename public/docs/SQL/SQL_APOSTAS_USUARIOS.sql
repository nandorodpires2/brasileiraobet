/* APOSTAS USUARIOS */
select	u.usuario_nome,
			count(*) as apostas
from		aposta a
			inner join partida p on a.partida_id = p.partida_id
			inner join usuario u on a.usuario_id = u.usuario_id	
where		p.partida_rodada = 12
group by u.usuario_id
order by count(*) desc