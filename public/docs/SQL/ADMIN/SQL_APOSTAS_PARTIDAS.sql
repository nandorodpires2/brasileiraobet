/* APOSTAS PARTIDAS */
select	a.aposta_id,
			u.usuario_nome,
			u.usuario_email,
			p.partida_id,
			p.partida_serie,
			p.partida_data,
			t1.time_nome,
			t2.time_nome,			
			concat(a.aposta_placar_mandante, ' - ', a.aposta_placar_visitante) as placar		
from		aposta a
			inner join partida p on a.partida_id = p.partida_id
			inner join usuario u on a.usuario_id = u.usuario_id
			inner join time t1 on p.time_id_mandante = t1.time_id
			inner join time t2 on p.time_id_visitante = t2.time_id
where		p.partida_realizada = 0		
			-- and p.partida_data = '2016-07-24 11:00:00'	
order by p.partida_serie asc,
			p.partida_data asc,
			concat(a.aposta_placar_mandante, ' - ', a.aposta_placar_visitante) asc,
			u.usuario_id
			