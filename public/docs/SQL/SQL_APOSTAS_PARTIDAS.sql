/* APOSTAS PARTIDAS */
select	p.partida_id,
			u.usuario_nome,
			u.usuario_email,
			t1.time_nome,
			t2.time_nome,
			concat(a.aposta_placar_mandante, ' - ', a.aposta_placar_visitante) as placar
from		aposta a
			inner join partida p on a.partida_id = p.partida_id
			inner join usuario u on a.usuario_id = u.usuario_id
			inner join time t1 on p.time_id_mandante = t1.time_id
			inner join time t2 on p.time_id_visitante = t2.time_id
where		p.partida_rodada = 14
			and p.partida_serie = 1
order by p.partida_id,
			concat(a.aposta_placar_mandante, ' - ', a.aposta_placar_visitante) asc,			 
			u.usuario_id
			