/* SQL ULTIMAS APOSTAS */
select	a.aposta_data,
			u.usuario_nome,
			t1.time_nome,
			t2.time_nome,
			concat(a.aposta_placar_mandante, '-', a.aposta_placar_visitante) aposta
from		aposta a
			inner join usuario u on a.usuario_id = u.usuario_id
			inner join partida p on a.partida_id = p.partida_id
			inner join time t1 on p.time_id_mandante = t1.time_id
			inner join time t2 on p.time_id_visitante = t2.time_id
order by a.aposta_data desc
limit 	10