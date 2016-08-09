/* ULTIMAS PREMIAÇÕES */
select	p.partida_data,
			t1.time_nome,
			t2.time_nome,
			u.usuario_nome,
			concat(p.partida_placar_mandante, '-', p.partida_placar_visitante) as placar,
			concat(a.aposta_placar_mandante, '-', a.aposta_placar_visitante) as aposta,
			a.aposta_vencedora_premio,
			a.aposta_vencedora_valor			
from		aposta a
			inner join usuario u on a.usuario_id = u.usuario_id
			inner join partida p on a.partida_id = p.partida_id
			inner join time t1 on p.time_id_mandante = t1.time_id
			inner join time t2 on p.time_id_visitante = t2.time_id
where		a.aposta_vencedora = 1 
			and p.partida_realizada = 1
order by p.partida_data desc
limit		10